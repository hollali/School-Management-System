<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\Fee;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FeeController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $user = Auth::user();
        $query = Fee::with('student.user');

        if ($user->hasRole('Admin')) {
            // Admin sees all
        } elseif ($user->hasRole('Teacher')) {
            $studentIds = Student::whereHas('classes', fn($q) => $q->whereIn('class_id', $user->teacher?->classes->pluck('id') ?? []))
                ->pluck('id');
            $query->whereIn('student_id', $studentIds);
        } elseif ($user->hasRole('Student')) {
            $query->where('student_id', $user->student?->id);
        } elseif ($user->hasRole('Parent')) {
            $studentIds = $user->parentProfile?->students->pluck('id') ?? [];
            $query->whereIn('student_id', $studentIds);
        }

        $fees = $query->latest()->paginate(15);

        return view('fees.index', compact('fees'));
    }

    public function create()
    {
        $this->authorize('manage-users');

        $students = Student::with('user')->orderBy('id')->get();

        return view('fees.create', compact('students'));
    }

    public function store(Request $request)
    {
        $this->authorize('manage-users');

        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'invoice_number' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
        ]);

        $fee = Fee::create($data);

        ActivityLogger::log('fee-created', 'Fee', $fee->id, "Created fee for student #{$data['student_id']}: \${$data['amount']}");

        return redirect()->route('fees.index')->with('success', 'Fee record saved successfully.');
    }

    public function show(Fee $fee)
    {
        $user = Auth::user();

        if ($user->hasRole('Student') && $fee->student_id !== $user->student?->id) {
            abort(403);
        }
        if ($user->hasRole('Parent') && !$user->parentProfile?->students->pluck('id')->contains($fee->student_id)) {
            abort(403);
        }

        $fee->load('student.user', 'payments');

        return view('fees.show', compact('fee'));
    }

    public function edit(Fee $fee)
    {
        $this->authorize('manage-users');

        $students = Student::with('user')->orderBy('id')->get();

        return view('fees.edit', compact('fee', 'students'));
    }

    public function update(Request $request, Fee $fee)
    {
        $this->authorize('manage-users');

        $data = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'invoice_number' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
        ]);

        $fee->update($data);

        ActivityLogger::log('fee-updated', 'Fee', $fee->id, "Updated fee for student #{$data['student_id']}");

        return redirect()->route('fees.index')->with('success', 'Fee record updated successfully.');
    }

    public function destroy(Fee $fee)
    {
        $this->authorize('manage-users');

        ActivityLogger::log('fee-deleted', 'Fee', $fee->id, "Deleted fee #{$fee->id}");
        $fee->delete();

        return redirect()->route('fees.index')->with('success', 'Fee record deleted successfully.');
    }
}
