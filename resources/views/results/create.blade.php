<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-white">{{ __('New Result') }}</h2>
            <a href="{{ route('results.index') }}"
                class="bg-white/20 hover:bg-white/30 text-white rounded-xl px-4 py-2 text-sm font-medium backdrop-blur-sm inline-flex items-center">
                {{ __('Back to list') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 p-8">
                <form action="{{ route('results.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 gap-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Student</label>
                                <select name="student_id"
                                    class="block w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white" required>
                                    <option value="">Select student</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>{{ $student->user->name }}</option>
                                    @endforeach
                                </select>
                                @error('student_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Subject</label>
                                <select name="subject_id"
                                    class="block w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white" required>
                                    <option value="">Select subject</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                                @error('subject_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Exam</label>
                                <select name="exam_id"
                                    class="block w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white" required>
                                    <option value="">Select exam</option>
                                    @foreach($exams as $exam)
                                        <option value="{{ $exam->id }}" {{ old('exam_id') == $exam->id ? 'selected' : '' }}>{{ $exam->name }}</option>
                                    @endforeach
                                </select>
                                @error('exam_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Score</label>
                                <input type="number" name="score" value="{{ old('score') }}" step="0.01"
                                    class="block w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4"
                                    required>
                                @error('score')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Remarks</label>
                            <textarea name="remarks" rows="3"
                                class="block w-full rounded-xl border-gray-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">{{ old('remarks') }}</textarea>
                            @error('remarks')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('results.index') }}"
                                class="inline-flex items-center px-6 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition">Cancel</a>
                            <button type="submit"
                                class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">Create result</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
