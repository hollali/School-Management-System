<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\Assignment;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $user = Auth::user();

        $query = Assignment::with(['teacher.user', 'schoolClass', 'subject']);

        if ($user->hasRole('Teacher')) {
            $query->where('teacher_id', $user->teacher?->id);
        } elseif ($user->hasRole('Student')) {
            $classIds = $user->student?->classes->pluck('id') ?? [];
            $query->whereIn('class_id', $classIds);
        } elseif ($user->hasRole('Parent')) {
            $classIds = $user->parentProfile?->students->flatMap->classes->pluck('id') ?? [];
            $query->whereIn('class_id', $classIds);
        }

        $assignments = $query->latest()->paginate(15);

        return view('assignments.index', compact('assignments'));
    }

    public function create()
    {
        $this->authorize('create', Assignment::class);

        $teacher = Auth::user()->teacher;
        $classes = SchoolClass::where('teacher_id', $teacher->id)->latest()->get();
        $subjects = Subject::latest()->get();

        return view('assignments.create', compact('classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Assignment::class);

        $data = $request->validate([
            'class_id'   => ['required', 'exists:classes,id'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'title'      => ['required', 'string', 'max:255'],
            'description'=> ['nullable', 'string'],
            'due_date'   => ['nullable', 'date'],
        ]);

        $data['teacher_id'] = Auth::user()->teacher->id;

        $assignment = Assignment::create($data);

        ActivityLogger::log('assignment-created', 'Assignment', $assignment->id, "Created assignment: {$assignment->title}");

        return redirect()->route('assignments.index')
            ->with('success', 'Assignment created successfully.');
    }

    public function show(Assignment $assignment)
    {
        $this->authorize('view', $assignment);
        $assignment->load(['submissions.student.user', 'teacher.user', 'schoolClass', 'subject']);

        return view('assignments.show', compact('assignment'));
    }

    public function edit(Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        $teacher = Auth::user()->teacher;
        $classes = SchoolClass::where('teacher_id', $teacher->id)->latest()->get();
        $subjects = Subject::latest()->get();

        return view('assignments.edit', compact('assignment', 'classes', 'subjects'));
    }

    public function update(Request $request, Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        $data = $request->validate([
            'class_id'   => ['required', 'exists:classes,id'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'title'      => ['required', 'string', 'max:255'],
            'description'=> ['nullable', 'string'],
            'due_date'   => ['nullable', 'date'],
        ]);

        $assignment->update($data);

        ActivityLogger::log('assignment-updated', 'Assignment', $assignment->id, "Updated assignment: {$assignment->title}");

        return redirect()->route('assignments.index')
            ->with('success', 'Assignment updated successfully.');
    }

    public function destroy(Assignment $assignment)
    {
        $this->authorize('delete', $assignment);

        ActivityLogger::log('assignment-deleted', 'Assignment', $assignment->id, "Deleted assignment: {$assignment->title}");
        $assignment->delete();

        return redirect()->route('assignments.index')
            ->with('success', 'Assignment deleted successfully.');
    }
}
