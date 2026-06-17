<x-app-layout>
    @section('title', 'Students')

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Students</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Manage all student records</p>
        </div>
    </x-slot>

    <div class="py-6">
        <x-data-table
            title="Students"
            :data="$students"
            searchable="true"
            searchPlaceholder="Search by name, email, or admission number..."
            searchValue="{{ request('search') }}"
            searchRoute="{{ route('students.index') }}"
        >
            <x-slot name="actions">
                @if(Auth::user()->hasRole('Admin'))
                    <button @click="$dispatch('open-modal', 'create-student')" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        <i class="fa-solid fa-plus mr-2"></i>
                        {{ __('New Student') }}
                    </button>
                @endif
                <a href="{{ route('students.export-csv') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition shadow-sm">
                    <i class="fa-solid fa-file-csv mr-2"></i>
                    {{ __('Export CSV') }}
                </a>
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition shadow-sm">
                    <i class="fa-solid fa-print mr-2"></i>
                    {{ __('Print') }}
                </button>
            </x-slot>

            <x-slot name="filters">
                @if($classes->count())
                <form action="{{ route('students.index') }}" method="GET" class="inline">
                    <select name="class_id" onchange="this.form.submit()" class="block rounded-lg border-gray-300 dark:border-slate-600 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-white dark:bg-slate-700 dark:text-slate-200">
                        <option value="">{{ __('All Classes') }}</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                    @foreach(request()->except('class_id', 'page') as $key => $value)
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

                <form action="{{ route('students.index') }}" method="GET" class="inline">
                    <select name="gender" onchange="this.form.submit()" class="block rounded-lg border-gray-300 dark:border-slate-600 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-white dark:bg-slate-700 dark:text-slate-200">
                        <option value="">{{ __('All Genders') }}</option>
                        <option value="Male" {{ request('gender') === 'Male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                        <option value="Female" {{ request('gender') === 'Female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                        <option value="Other" {{ request('gender') === 'Other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                    </select>
                    @foreach(request()->except('gender', 'page') as $key => $value)
                        @if(is_array($value))
                            @foreach($value as $v)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                </form>

                @if(isset($parents) && $parents->count())
                <form action="{{ route('students.index') }}" method="GET" class="inline">
                    <select name="parent_id" onchange="this.form.submit()" class="block rounded-lg border-gray-300 dark:border-slate-600 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-white dark:bg-slate-700 dark:text-slate-200">
                        <option value="">{{ __('All Parents') }}</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}" {{ request('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->user->name }}</option>
                        @endforeach
                    </select>
                    @foreach(request()->except('parent_id', 'page') as $key => $value)
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
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Email</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Admission</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Class</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Parent</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($students as $student)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $student->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $student->user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $student->admission_number ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $student->classes->pluck('name')->join(', ') ?: 'Unassigned' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $student->parent?->user?->name ?? 'None' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                            <button @click="$dispatch('view-student', { id: '{{ $student->id }}', name: '{{ $student->user->name }}', email: '{{ $student->user->email }}', admission: '{{ $student->admission_number ?? 'N/A' }}', gender: '{{ $student->gender ?? 'N/A' }}', dob: '{{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : 'N/A' }}', phone: '{{ $student->phone ?? 'N/A' }}', address: '{{ $student->address ?? 'N/A' }}', classes: '{{ $student->classes->pluck('name')->join(', ') ?: 'Not Assigned' }}', parent: '{{ $student->parent?->user?->name ?? 'None' }}', since: '{{ $student->created_at->format('M d, Y') }}' })" title="View" class="inline-flex items-center justify-center w-8 h-8 text-gray-400 dark:text-slate-500 hover:text-white hover:bg-gray-500 rounded-lg transition">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            @if(Auth::user()->hasRole('Admin'))
                                <button @click="$dispatch('edit-student', { id: '{{ $student->id }}', name: '{{ $student->user->name }}', email: '{{ $student->user->email }}', admission: '{{ $student->admission_number }}', dob: '{{ $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '' }}', gender: '{{ $student->gender }}', phone: '{{ $student->phone }}', address: '{{ $student->address }}', parent_id: '{{ $student->parent_id }}', class_id: '{{ $student->classes->first()?->id }}' })" title="Edit" class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button @click="$dispatch('set-confirmation', {
                                    action: '{{ route('students.destroy', $student) }}',
                                    method: 'DELETE',
                                    title: 'Delete Student',
                                    message: 'Delete this student profile? This action cannot be undone.',
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
                        <td colspan="6" class="text-center py-12 text-gray-400 dark:text-slate-500">No students found.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>

    {{-- Create Student Modal --}}
    <x-modal name="create-student" maxWidth="2xl" focusable>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ __('New Student') }}</h2>
                <button @click="$dispatch('close-modal', 'create-student')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('students.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Name') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                </div>
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="Student name" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700" required>
                            </div>
                            @error('name')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Email') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                                </div>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="student@example.com" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700" required>
                            </div>
                            @error('email')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Password') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                                </div>
                                <input type="password" name="password" placeholder="Min. 8 characters" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700" required>
                            </div>
                            @error('password')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Confirm Password') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                                </div>
                                <input type="password" name="password_confirmation" placeholder="Repeat password" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700" required>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Admission #') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" /></svg>
                                </div>
                                <input type="text" name="admission_number" value="{{ old('admission_number') }}" placeholder="e.g. ADM-2024-001" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700">
                            </div>
                            @error('admission_number')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Date of birth') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                                </div>
                                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700">
                            </div>
                            @error('date_of_birth')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Gender') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                </div>
                                <select name="gender" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                                    <option value="">{{ __('Select') }}</option>
                                    <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                                    <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                                    <option value="Other" {{ old('gender') === 'Other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                                </select>
                            </div>
                            @error('gender')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Phone') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                                </div>
                                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+1 (555) 000-0000" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700">
                            </div>
                            @error('phone')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Address') }}</label>
                        <div class="relative">
                            <div class="absolute top-3 left-0 pl-3.5 flex items-start pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                            </div>
                            <textarea name="address" rows="3" placeholder="Full address" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700">{{ old('address') }}</textarea>
                        </div>
                        @error('address')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Parent') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                                </div>
                                <select name="parent_id" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                                    <option value="">{{ __('None') }}</option>
                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->user->name }} ({{ $parent->relationship ?: 'Parent' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('parent_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Class') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342" /></svg>
                                </div>
                                <select name="class_id" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                                    <option value="">{{ __('Unassigned') }}</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }} {{ $class->section ? ' - '.$class->section : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('class_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="$dispatch('close-modal', 'create-student')" class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">{{ __('Cancel') }}</button>
                        <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition shadow-sm">{{ __('Create student') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    {{-- Edit Student Modal --}}
    <x-modal name="edit-student" maxWidth="2xl" focusable>
        <div class="p-6" x-data="editStudentData()" @edit-student.window="load($event.detail)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ __('Edit Student') }}</h2>
                <button @click="$dispatch('close-modal', 'edit-student')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form method="POST" :action="`/students/${form.id}`">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Name') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                </div>
                                <input type="text" name="name" x-model="form.name" placeholder="Student name" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Email') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                                </div>
                                <input type="email" name="email" x-model="form.email" placeholder="student@example.com" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700" required>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('New Password') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                                </div>
                                <input type="password" name="password" placeholder="Leave blank to keep current" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Confirm Password') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                                </div>
                                <input type="password" name="password_confirmation" placeholder="Repeat new password" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700">
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Admission #') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" /></svg>
                                </div>
                                <input type="text" name="admission_number" x-model="form.admission" placeholder="e.g. ADM-2024-001" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Date of birth') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                                </div>
                                <input type="date" name="date_of_birth" x-model="form.dob" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700">
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Gender') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                </div>
                                <select name="gender" x-model="form.gender" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                                    <option value="">{{ __('Select') }}</option>
                                    <option value="Male">{{ __('Male') }}</option>
                                    <option value="Female">{{ __('Female') }}</option>
                                    <option value="Other">{{ __('Other') }}</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Phone') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                                </div>
                                <input type="text" name="phone" x-model="form.phone" placeholder="+1 (555) 000-0000" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700">
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Address') }}</label>
                        <div class="relative">
                            <div class="absolute top-3 left-0 pl-3.5 flex items-start pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                            </div>
                            <textarea name="address" x-model="form.address" rows="3" placeholder="Full address" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700"></textarea>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Parent') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                                </div>
                                <select name="parent_id" x-model="form.parent_id" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                                    <option value="">{{ __('None') }}</option>
                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}">{{ $parent->user->name }} ({{ $parent->relationship ?: 'Parent' }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Class') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342" /></svg>
                                </div>
                                <select name="class_id" x-model="form.class_id" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200">
                                    <option value="">{{ __('Unassigned') }}</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }} {{ $class->section ? ' - '.$class->section : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="$dispatch('close-modal', 'edit-student')" class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">{{ __('Cancel') }}</button>
                        <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition shadow-sm">{{ __('Save changes') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    {{-- View Student Modal --}}
    <x-modal name="view-student" maxWidth="2xl" focusable>
        <div class="p-6" x-data="viewStudentData()" @view-student.window="load($event.detail)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Student Profile</h2>
                <button @click="$dispatch('close-modal', 'view-student')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-slate-200 mb-4">Personal Information</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-slate-400">Name</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-slate-200" x-text="data.name"></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-slate-400">Email</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-slate-200" x-text="data.email"></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-slate-400">Gender</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-slate-200" x-text="data.gender"></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-slate-400">Date of Birth</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-slate-200" x-text="data.dob"></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-slate-400">Phone</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-slate-200" x-text="data.phone"></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-slate-400">Address</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-slate-200" x-text="data.address"></dd>
                        </div>
                    </dl>
                </div>
                <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-slate-200 mb-4">Academic Information</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-slate-400">Admission Number</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-slate-200" x-text="data.admission"></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-slate-400">Classes</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-slate-200" x-text="data.classes"></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-slate-400">Parent/Guardian</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-slate-200" x-text="data.parent"></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500 dark:text-slate-400">Member Since</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-slate-200" x-text="data.since"></dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </x-modal>

    <script>
        function editStudentData() {
            return {
                form: { id: '', name: '', email: '', admission: '', dob: '', gender: '', phone: '', address: '', parent_id: '', class_id: '' },
                load(data) {
                    this.form = { ...data };
                    this.$dispatch('open-modal', 'edit-student');
                }
            };
        }
        function viewStudentData() {
            return {
                data: { id: '', name: '', email: '', admission: '', gender: '', dob: '', phone: '', address: '', classes: '', parent: '', since: '' },
                load(data) {
                    this.data = { ...data };
                    this.$dispatch('open-modal', 'view-student');
                }
            };
        }
    </script>
</x-app-layout>
