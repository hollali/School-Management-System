<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Student::with(['user', 'parent.user', 'classes'])
            ->latest();

        // Filtering
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('admission_number', 'like', "%{$search}%");
        }

        if ($request->has('class_id')) {
            $query->whereHas('classes', function ($q) use ($request) {
                $q->where('classes.id', $request->input('class_id'));
            });
        }

        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->input('parent_id'));
        }

        $students = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $students->items(),
            'pagination' => [
                'total' => $students->total(),
                'per_page' => $students->perPage(),
                'current_page' => $students->currentPage(),
                'last_page' => $students->lastPage(),
            ],
        ]);
    }

    public function store(StoreStudentRequest $request): JsonResponse
    {
        try {
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

            $student->load(['user', 'parent.user', 'classes']);

            return response()->json([
                'success' => true,
                'message' => 'Student created successfully',
                'data' => $student,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating student',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Student $student): JsonResponse
    {
        $student->load(['user', 'parent.user', 'classes']);

        return response()->json([
            'success' => true,
            'data' => $student,
        ]);
    }

    public function update(UpdateStudentRequest $request, Student $student): JsonResponse
    {
        try {
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

            $student->load(['user', 'parent.user', 'classes']);

            return response()->json([
                'success' => true,
                'message' => 'Student updated successfully',
                'data' => $student,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating student',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Student $student): JsonResponse
    {
        try {
            // Delete associated user
            $student->user()->delete();
            $student->delete();

            return response()->json([
                'success' => true,
                'message' => 'Student deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting student',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getClasses(Student $student): JsonResponse
    {
        $classes = $student->classes()->with('subjects')->get();

        return response()->json([
            'success' => true,
            'data' => $classes,
        ]);
    }

    public function getAttendance(Student $student): JsonResponse
    {
        $attendance = $student->user()
            ->with('attendanceRecords')
            ->first()
            ->attendanceRecords()
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $attendance->items(),
            'pagination' => [
                'total' => $attendance->total(),
                'per_page' => $attendance->perPage(),
                'current_page' => $attendance->currentPage(),
            ],
        ]);
    }

    public function getGrades(Student $student): JsonResponse
    {
        $grades = $student->user()
            ->with('results.exam')
            ->first()
            ->results()
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $grades,
        ]);
    }

    public function assignClass(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'status' => ['nullable', 'in:active,inactive,transferred'],
        ]);

        try {
            $student->classes()->sync([
                $validated['class_id'] => [
                    'assigned_at' => now(),
                    'status' => $validated['status'] ?? 'active',
                ],
            ]);

            $student->load('classes');

            return response()->json([
                'success' => true,
                'message' => 'Class assigned successfully',
                'data' => $student,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning class',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function bulkImport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        try {
            $file = $request->file('csv_file');
            $stream = fopen($file->path(), 'r');
            $header = fgetcsv($stream);
            $imported = 0;
            $errors = [];

            while (($row = fgetcsv($stream)) !== false) {
                try {
                    $data = array_combine($header, $row);

                    $user = User::create([
                        'name' => $data['name'] ?? 'N/A',
                        'email' => $data['email'],
                        'password' => Hash::make($data['password'] ?? 'default123'),
                    ]);

                    $user->assignRole('Student');

                    Student::create([
                        'user_id' => $user->id,
                        'admission_number' => $data['admission_number'] ?? null,
                        'date_of_birth' => $data['date_of_birth'] ?? null,
                        'gender' => $data['gender'] ?? null,
                        'phone' => $data['phone'] ?? null,
                        'address' => $data['address'] ?? null,
                        'parent_id' => $data['parent_id'] ?? null,
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row error: {$e->getMessage()}";
                }
            }

            fclose($stream);

            return response()->json([
                'success' => true,
                'message' => "Imported {$imported} students",
                'imported' => $imported,
                'errors' => $errors,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk import failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
