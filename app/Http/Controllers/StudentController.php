<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\ParentProfile;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $query = Student::with(['user', 'parent.user', 'classes']);

        // Filtering
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->searchByName($search)
                  ->orSearchByAdmissionNumber($search)
                  ->orSearchByEmail($search);
            });
        }

        if ($request->has('class_id')) {
            $query->byClass($request->input('class_id'));
        }

        if ($request->has('parent_id')) {
            $query->byParent($request->input('parent_id'));
        }

        if ($request->has('gender')) {
            $query->byGender($request->input('gender'));
        }

        $students = $query->latest()->paginate(15)->appends($request->query());

        return view('students.index', compact('students'));
    }

    public function create(): View
    {
        $classes = SchoolClass::orderBy('name')->get();
        $parents = ParentProfile::with('user')->orderBy('id')->get();

        return view('students.create', compact('classes', 'parents'));
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole('Student');

        // Create student record
        $student = Student::create([
            'user_id' => $user->id,
            'admission_number' => $validated['admission_number'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        // Assign to class if provided
        if (!empty($validated['class_id'])) {
            $student->classes()->sync([
                $validated['class_id'] => [
                    'assigned_at' => now(),
                    'status' => 'active',
                ],
            ]);
        }

        return redirect()->route('students.index')
            ->with('success', 'Student profile created successfully.');
    }

    public function show(Student $student): View
    {
        $student->load(['user', 'parent.user', 'classes.subjects']);

        return view('students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        $classes = SchoolClass::orderBy('name')->get();
        $parents = ParentProfile::with('user')->orderBy('id')->get();

        return view('students.edit', compact('student', 'classes', 'parents'));
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $validated = $request->validated();

        // Update user
        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $student->user()->update($updateData);

        // Update student
        $student->update([
            'admission_number' => $validated['admission_number'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        // Update class assignment
        if (!empty($validated['class_id'])) {
            $student->classes()->sync([
                $validated['class_id'] => [
                    'assigned_at' => now(),
                    'status' => 'active',
                ],
            ]);
        } else {
            $student->classes()->detach();
        }

        return redirect()->route('students.index')
            ->with('success', 'Student profile updated successfully.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->user()->delete();
        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Student profile deleted successfully.');
    }
}
