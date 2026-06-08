@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Student Profile</h1>
                    <div class="space-x-2">
                        <a href="{{ route('students.edit', $student) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                            Edit
                        </a>
                        <a href="{{ route('students.index') }}" class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">
                            Back
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Personal Information -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h2 class="text-xl font-semibold mb-4">Personal Information</h2>
                        <div class="space-y-2">
                            <p><strong>Name:</strong> {{ $student->user->name }}</p>
                            <p><strong>Email:</strong> {{ $student->user->email }}</p>
                            <p><strong>Gender:</strong> {{ $student->gender ?? 'N/A' }}</p>
                            <p><strong>Date of Birth:</strong> {{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : 'N/A' }}</p>
                            <p><strong>Phone:</strong> {{ $student->phone ?? 'N/A' }}</p>
                            <p><strong>Address:</strong> {{ $student->address ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h2 class="text-xl font-semibold mb-4">Academic Information</h2>
                        <div class="space-y-2">
                            <p><strong>Admission Number:</strong> {{ $student->admission_number ?? 'N/A' }}</p>
                            <p><strong>Classes:</strong> {{ $student->classes->pluck('name')->join(', ') ?? 'Not Assigned' }}</p>
                            <p><strong>Parent/Guardian:</strong> {{ $student->parent?->user->name ?? 'Not Assigned' }}</p>
                            <p><strong>Member Since:</strong> {{ $student->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Classes Detail -->
                @if ($student->classes->count() > 0)
                    <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                        <h2 class="text-xl font-semibold mb-4">Assigned Classes</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full border-collapse">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="border px-4 py-2 text-left">Class Name</th>
                                        <th class="border px-4 py-2 text-left">Status</th>
                                        <th class="border px-4 py-2 text-left">Assigned Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($student->classes as $class)
                                        <tr>
                                            <td class="border px-4 py-2">{{ $class->name }}</td>
                                            <td class="border px-4 py-2">
                                                <span class="px-2 py-1 rounded text-sm {{ $class->pivot->status == 'active' ? 'bg-green-200 text-green-800' : 'bg-gray-200' }}">
                                                    {{ ucfirst($class->pivot->status) }}
                                                </span>
                                            </td>
                                            <td class="border px-4 py-2">{{ $class->pivot->assigned_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
