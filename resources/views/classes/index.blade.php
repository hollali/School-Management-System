<x-app-layout>
    @section('title', 'Classes')

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Classes</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Manage class groups and sections</p>
        </div>
    </x-slot>

    <div class="py-6">
        <x-data-table
            title="Classes"
            :data="$classes"
            searchable="true"
            searchPlaceholder="Search by name or section..."
            searchValue="{{ request('search') }}"
            searchRoute="{{ route('classes.index') }}"
        >
            <x-slot name="actions">
                @if(Auth::user()->hasRole('Admin'))
                    <button @click="$dispatch('open-modal', 'create-class')" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        <i class="fa-solid fa-plus mr-2"></i>
                        {{ __('New Class') }}
                    </button>
                @endif
            </x-slot>

            <x-slot name="filters">
                @if(isset($teachers) && $teachers->count())
                <form action="{{ route('classes.index') }}" method="GET" class="inline">
                    <select name="teacher_id" onchange="this.form.submit()" class="block rounded-lg border-gray-300 dark:border-slate-600 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-white dark:bg-slate-700 dark:text-slate-200">
                        <option value="">{{ __('All Teachers') }}</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->user->name }}</option>
                        @endforeach
                    </select>
                    @foreach(request()->except('teacher_id', 'page') as $key => $value)
                        @if(is_array($value))
                            @foreach($value as $v)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                </form>
                @endif
            </x-slot>

            <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Grade</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Section</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Teacher</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Students</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($classes as $class)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $class->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $class->grade_level ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $class->section ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $class->teacher?->user?->name ?? 'Unassigned' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $class->students->count() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                            <button @click="
                                $dispatch('view-class', {
                                    name: '{{ $class->name }}',
                                    grade_level: '{{ $class->grade_level ?? 'N/A' }}',
                                    section: '{{ $class->section ?? 'N/A' }}',
                                    teacher: '{{ $class->teacher?->user?->name ?? 'Unassigned' }}',
                                    students: '{{ $class->students->count() }}',
                                    capacity: '{{ $class->capacity ?? 'N/A' }}',
                                    academic_year: '{{ $class->academic_year ?? 'N/A' }}'
                                })
                            " title="View" class="inline-flex items-center justify-center w-8 h-8 text-gray-400 dark:text-slate-500 hover:text-white hover:bg-gray-500 rounded-lg transition">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            @if(Auth::user()->hasRole('Admin'))
                                <button @click="
                                    $dispatch('edit-class', {
                                        id: '{{ $class->id }}',
                                        name: '{{ $class->name }}',
                                        grade_level: '{{ $class->grade_level }}',
                                        section: '{{ $class->section }}',
                                        teacher_id: '{{ $class->teacher_id }}',
                                        capacity: '{{ $class->capacity }}',
                                        academic_year: '{{ $class->academic_year }}'
                                    })
                                " title="Edit" class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button @click="$dispatch('set-confirmation', {
                                    action: '{{ route('classes.destroy', $class) }}',
                                    method: 'DELETE',
                                    title: 'Delete Class',
                                    message: 'Delete this class? This action cannot be undone.',
                                    confirmLabel: 'Delete',
                                    confirmClass: 'bg-red-600 hover:bg-red-700'
                                })" title="Delete" class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:text-white hover:bg-red-500 rounded-lg transition">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-gray-400 dark:text-slate-500">No classes found.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>

    <x-modal name="create-class" maxWidth="2xl" focusable>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ __('New Class') }}</h2>
                <button @click="$dispatch('close-modal', 'create-class')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('classes.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Name') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                            </div>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Class name" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700" required>
                        </div>
                        @error('name')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Grade Level') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75H17.25m0 0A2.25 2.25 0 0119.5 15v3m-2.25-3.75h3.75M21 19.5a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 19.5V6a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 6v3.75" /></svg>
                                </div>
                                <input type="text" name="grade_level" value="{{ old('grade_level') }}" placeholder="e.g. Grade 10" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700">
                            </div>
                            @error('grade_level')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Section') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 01-1.125-1.125v-3.75zM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-8.25zM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-2.25z" /></svg>
                                </div>
                                <input type="text" name="section" value="{{ old('section') }}" placeholder="e.g. Section A" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700">
                            </div>
                            @error('section')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Teacher') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                </div>
                                <select name="teacher_id" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                                    <option value="">{{ __('Unassigned') }}</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('teacher_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Capacity') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                                </div>
                                <input type="number" name="capacity" value="{{ old('capacity') }}" placeholder="Max students" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700">
                            </div>
                            @error('capacity')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Academic Year') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                            </div>
                            <input type="text" name="academic_year" value="{{ old('academic_year') }}" placeholder="e.g. 2024-2025" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700">
                        </div>
                        @error('academic_year')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button @click="$dispatch('close-modal', 'create-class')" type="button" class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">{{ __('Cancel') }}</button>
                        <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition shadow-sm">{{ __('Create class') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="edit-class" maxWidth="2xl" focusable>
        <div class="p-6" x-data="editClassData()" @edit-class.window="load($event.detail)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ __('Edit Class') }}</h2>
                <button @click="$dispatch('close-modal', 'edit-class')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form method="POST" :action="`/classes/${form.id}`">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Name') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                            </div>
                            <input type="text" name="name" x-model="form.name" placeholder="Class name" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Grade Level') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4.5h14.25M3 9h9.75M3 13.5h5.25m5.25-.75H17.25m0 0A2.25 2.25 0 0119.5 15v3m-2.25-3.75h3.75M21 19.5a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 19.5V6a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 6v3.75" /></svg>
                                </div>
                                <input type="text" name="grade_level" x-model="form.grade_level" placeholder="e.g. Grade 10" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Section') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 01-1.125-1.125v-3.75zM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-8.25zM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-2.25z" /></svg>
                                </div>
                                <input type="text" name="section" x-model="form.section" placeholder="e.g. Section A" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Teacher') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                </div>
                                <select name="teacher_id" x-model="form.teacher_id" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                                    <option value="">{{ __('Unassigned') }}</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Capacity') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                                </div>
                                <input type="number" name="capacity" x-model="form.capacity" placeholder="Max students" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Academic Year') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                            </div>
                            <input type="text" name="academic_year" x-model="form.academic_year" placeholder="e.g. 2024-2025" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button @click="$dispatch('close-modal', 'edit-class')" type="button" class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">{{ __('Cancel') }}</button>
                        <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition shadow-sm">{{ __('Save changes') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="view-class" maxWidth="2xl" focusable>
        <div class="p-6" x-data="viewClassData()" @view-class.window="load($event.detail)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ __('View Class') }}: <span x-text="data.name"></span></h2>
                <button @click="$dispatch('close-modal', 'view-class')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">{{ __('Grade Level') }}</label>
                    <p class="text-sm text-gray-900 dark:text-slate-200" x-text="data.grade_level"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">{{ __('Section') }}</label>
                    <p class="text-sm text-gray-900 dark:text-slate-200" x-text="data.section"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">{{ __('Teacher') }}</label>
                    <p class="text-sm text-gray-900 dark:text-slate-200" x-text="data.teacher"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">{{ __('Students') }}</label>
                    <p class="text-sm text-gray-900 dark:text-slate-200" x-text="data.students"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">{{ __('Capacity') }}</label>
                    <p class="text-sm text-gray-900 dark:text-slate-200" x-text="data.capacity"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">{{ __('Academic Year') }}</label>
                    <p class="text-sm text-gray-900 dark:text-slate-200" x-text="data.academic_year"></p>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button @click="$dispatch('close-modal', 'view-class')" type="button" class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">{{ __('Close') }}</button>
            </div>
        </div>
    </x-modal>

    <script>
    function editClassData() {
        return {
            form: { id: '', name: '', grade_level: '', section: '', teacher_id: '', capacity: '', academic_year: '' },
            load(data) {
                this.form = { ...data };
                this.$dispatch('open-modal', 'edit-class');
            }
        };
    }
    function viewClassData() {
        return {
            data: { name: '', grade_level: '', section: '', teacher: '', students: '', capacity: '', academic_year: '' },
            load(data) {
                this.data = { ...data };
                this.$dispatch('open-modal', 'view-class');
            }
        };
    }
    </script>
</x-app-layout>
