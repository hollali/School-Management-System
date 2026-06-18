<x-app-layout>
    @section('title', __('Edit Announcement'))

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">{{ __('Edit Announcement') }}</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">{{ $announcement->title }}</p>
            </div>
            <a href="{{ route('announcements.index') }}"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-8">
                <form action="{{ route('announcements.update', $announcement) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-6">
                        <x-input-label for="title" :value="__('Title')" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $announcement->title)" required autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <x-input-label for="body" :value="__('Message')" />
                        <textarea id="body" name="body" rows="8"
                            class="mt-1 block w-full border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-xl shadow-sm text-sm"
                            required>{{ old('body', $announcement->body) }}</textarea>
                        <x-input-error :messages="$errors->get('body')" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <x-input-label :value="__('Target Audience')" />
                        <p class="text-xs text-gray-400 dark:text-slate-500 mb-3">Choose who should receive this announcement</p>

                        @if(Auth::user()->hasRole('Teacher'))
                            <input type="hidden" name="target_role" value="student">
                            <div class="space-y-3">
                                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-slate-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                                    <input type="radio" name="target_type" value="all_students" class="text-sky-600 focus:ring-sky-500"
                                        @checked(!$announcement->target_class_id && !$announcement->target_student_id)>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-slate-200">All Students</p>
                                        <p class="text-xs text-gray-400 dark:text-slate-500">Send to every student in the system</p>
                                    </div>
                                </label>

                                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-slate-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                                    <input type="radio" name="target_type" value="class" class="text-sky-600 focus:ring-sky-500"
                                        @checked($announcement->target_class_id && !$announcement->target_student_id)>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-slate-200">Specific Class</p>
                                        <p class="text-xs text-gray-400 dark:text-slate-500">Send to all students in a class</p>
                                    </div>
                                </label>

                                <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-slate-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                                    <input type="radio" name="target_type" value="student" class="text-sky-600 focus:ring-sky-500"
                                        @checked($announcement->target_student_id)>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-slate-200">Individual Student</p>
                                        <p class="text-xs text-gray-400 dark:text-slate-500">Send to one specific student</p>
                                    </div>
                                </label>
                            </div>

                            <div class="mt-4" id="class-select" style="{{ $announcement->target_class_id && !$announcement->target_student_id ? '' : 'display: none;' }}">
                                <x-input-label for="target_class_id" :value="__('Select Class')" />
                                <select id="target_class_id" name="target_class_id"
                                    class="mt-1 block w-full border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-xl shadow-sm text-sm">
                                    <option value="">Choose a class...</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" @selected(old('target_class_id', $announcement->target_class_id) == $class->id)>{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('target_class_id')" class="mt-2" />
                            </div>

                            <div class="mt-4" id="student-select" style="{{ $announcement->target_student_id ? '' : 'display: none;' }}">
                                <x-input-label for="target_student_id" :value="__('Select Student')" />
                                <select id="target_student_id" name="target_student_id"
                                    class="mt-1 block w-full border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-xl shadow-sm text-sm">
                                    <option value="">Choose a student...</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" @selected(old('target_student_id', $announcement->target_student_id) == $student->id)>{{ $student->user?->name }} ({{ $student->admission_number }})</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('target_student_id')" class="mt-2" />
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="target_role" :value="__('Target Role')" />
                                    <select id="target_role" name="target_role"
                                        class="mt-1 block w-full border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-xl shadow-sm text-sm">
                                        <option value="">All Users</option>
                                        <option value="student" @selected(old('target_role', $announcement->target_role) === 'student')>Students Only</option>
                                        <option value="teacher" @selected(old('target_role', $announcement->target_role) === 'teacher')>Teachers Only</option>
                                        <option value="admin" @selected(old('target_role', $announcement->target_role) === 'admin')>Administrators Only</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('target_role')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="target_class_id" :value="__('Specific Class (optional)')" />
                                    <select id="target_class_id" name="target_class_id"
                                        class="mt-1 block w-full border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-xl shadow-sm text-sm">
                                        <option value="">All Classes</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" @selected(old('target_class_id', $announcement->target_class_id) == $class->id)>{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('target_class_id')" class="mt-2" />
                                </div>
                            </div>

                            <div class="mt-4">
                                <x-input-label for="target_student_id" :value="__('Individual Student (optional)')" />
                                <select id="target_student_id" name="target_student_id"
                                    class="mt-1 block w-full border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-sky-500 focus:ring-sky-500 rounded-xl shadow-sm text-sm">
                                    <option value="">No specific student</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" @selected(old('target_student_id', $announcement->target_student_id) == $student->id)>{{ $student->user?->name }} ({{ $student->admission_number }})</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('target_student_id')" class="mt-2" />
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit"
                            class="inline-flex items-center px-6 py-3 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-xl transition">
                            <i class="fa-solid fa-save mr-2"></i>
                            {{ __('Update Announcement') }}
                        </button>
                        <a href="{{ route('announcements.index') }}"
                            class="text-sm text-gray-500 dark:text-slate-400 hover:text-gray-700 dark:hover:text-slate-200 transition">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
    document.querySelectorAll('input[name="target_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('class-select').style.display = this.value === 'class' ? '' : 'none';
            document.getElementById('student-select').style.display = this.value === 'student' ? '' : 'none';
        });
    });
</script>
@endpush
