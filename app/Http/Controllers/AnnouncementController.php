<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\Announcement;
use App\Models\AppNotification;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Announcement::class);

        $user = Auth::user();

        $query = Announcement::with('publisher', 'targetClass', 'targetStudent.user')->published();

        if ($user->hasRole('Teacher')) {
            $query->where(function ($q) use ($user) {
                $q->whereNull('target_role')
                  ->orWhere('target_role', 'student')
                  ->orWhere('published_by', $user->id);
            });
        } elseif (!$user->hasRole('Admin')) {
            $query->where(function ($q) use ($user) {
                $q->whereNull('target_role')
                  ->orWhereRaw('LOWER(target_role) = ?', [strtolower($user->role)]);
            });
        }

        $announcements = $query->latest('published_at')->paginate(15);

        return view('announcements.index', compact('announcements'));
    }

    public function create()
    {
        $this->authorize('create', Announcement::class);

        $user = Auth::user();
        $classes = SchoolClass::latest()->get();

        if ($user->hasRole('Teacher')) {
            $classes = $user->teacher?->classes ?? collect();
            $students = Student::whereHas('classes', function ($q) use ($classes) {
                $q->whereIn('classes.id', $classes->pluck('id'));
            })->with('user')->get();
        } else {
            $students = Student::with('user')->latest()->get();
        }

        return view('announcements.create', compact('classes', 'students'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Announcement::class);

        $user = Auth::user();

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'body'  => ['required', 'string'],
        ];

        if ($user->hasRole('Admin')) {
            $rules['target_role'] = ['nullable', 'string', 'in:student,teacher,admin'];
            $rules['target_class_id'] = ['nullable', 'exists:classes,id'];
            $rules['target_student_id'] = ['nullable', 'exists:students,id'];
        } else {
            $rules['target_role'] = ['nullable', 'string', 'in:student'];
            $rules['target_class_id'] = ['nullable', 'exists:classes,id'];
            $rules['target_student_id'] = ['nullable', 'exists:students,id'];
        }

        $data = $request->validate($rules);

        if ($user->hasRole('Teacher')) {
            $data['target_role'] = $data['target_role'] ?? 'student';

            if ($data['target_class_id']) {
                $teacherClassIds = $user->teacher?->classes->pluck('id')->toArray() ?? [];
                if (!in_array($data['target_class_id'], $teacherClassIds)) {
                    return back()->withErrors(['target_class_id' => 'You can only target your own classes.'])->withInput();
                }
            }

            if ($data['target_student_id']) {
                $student = Student::find($data['target_student_id']);
                if (!$student || !$student->classes()->whereIn('classes.id', $user->teacher?->classes->pluck('id') ?? [])->exists()) {
                    return back()->withErrors(['target_student_id' => 'You can only target students in your classes.'])->withInput();
                }
            }
        }

        $data['published_by'] = $user->id;
        $data['published_at'] = now();

        $announcement = Announcement::create($data);

        event(new \App\Events\AnnouncementPublished($announcement));

        ActivityLogger::log('announcement-created', 'Announcement', $announcement->id, "Published announcement: {$announcement->title}");

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement published successfully.');
    }

    public function show(Announcement $announcement)
    {
        $this->authorize('view', $announcement);

        $announcement->load('publisher', 'targetClass', 'targetStudent.user');

        return view('announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        $user = Auth::user();
        $classes = SchoolClass::latest()->get();

        if ($user->hasRole('Teacher')) {
            $classes = $user->teacher?->classes ?? collect();
            $students = Student::whereHas('classes', function ($q) use ($classes) {
                $q->whereIn('classes.id', $classes->pluck('id'));
            })->with('user')->get();
        } else {
            $students = Student::with('user')->latest()->get();
        }

        return view('announcements.edit', compact('announcement', 'classes', 'students'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        $user = Auth::user();

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'body'  => ['required', 'string'],
        ];

        if ($user->hasRole('Admin')) {
            $rules['target_role'] = ['nullable', 'string', 'in:student,teacher,admin'];
            $rules['target_class_id'] = ['nullable', 'exists:classes,id'];
            $rules['target_student_id'] = ['nullable', 'exists:students,id'];
        } else {
            $rules['target_role'] = ['nullable', 'string', 'in:student'];
            $rules['target_class_id'] = ['nullable', 'exists:classes,id'];
            $rules['target_student_id'] = ['nullable', 'exists:students,id'];
        }

        $data = $request->validate($rules);

        if ($user->hasRole('Teacher')) {
            $data['target_role'] = $data['target_role'] ?? 'student';

            if ($data['target_class_id']) {
                $teacherClassIds = $user->teacher?->classes->pluck('id')->toArray() ?? [];
                if (!in_array($data['target_class_id'], $teacherClassIds)) {
                    return back()->withErrors(['target_class_id' => 'You can only target your own classes.'])->withInput();
                }
            }

            if ($data['target_student_id']) {
                $student = Student::find($data['target_student_id']);
                if (!$student || !$student->classes()->whereIn('classes.id', $user->teacher?->classes->pluck('id') ?? [])->exists()) {
                    return back()->withErrors(['target_student_id' => 'You can only target students in your classes.'])->withInput();
                }
            }
        }

        $announcement->update($data);

        AppNotification::where('type', 'announcement')
            ->where('data->announcement_id', $announcement->id)
            ->chunk(100, function ($notifications) use ($announcement) {
                foreach ($notifications as $notification) {
                    $notifData = $notification->data;
                    $wasRead = !is_null($notification->read_at);
                    $notifData['title'] = '✎ Updated: ' . $announcement->title;
                    $notifData['body'] = substr($announcement->body, 0, 300);
                    $notifData['updated'] = true;
                    $notifData['updated_at'] = now()->toISOString();
                    $notification->update([
                        'data' => $notifData,
                        'read_at' => $wasRead ? $notification->read_at : null,
                    ]);
                }
            });

        ActivityLogger::log('announcement-updated', 'Announcement', $announcement->id, "Updated announcement: {$announcement->title}");

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        $this->authorize('delete', $announcement);

        AppNotification::where('type', 'announcement')
            ->where('data->announcement_id', $announcement->id)
            ->delete();

        ActivityLogger::log('announcement-deleted', 'Announcement', $announcement->id, "Deleted announcement: {$announcement->title}");
        $announcement->delete();

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }
}
