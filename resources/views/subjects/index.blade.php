<x-app-layout>
    @section('title', 'Subjects')

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Subjects</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Manage academic subjects and curricula</p>
        </div>
    </x-slot>

    <div class="py-6">
        <x-data-table
            title="Subjects"
            :data="$subjects"
            searchable="true"
            searchPlaceholder="Search by name or code..."
            searchValue="{{ request('search') }}"
            searchRoute="{{ route('subjects.index') }}"
        >
            <x-slot name="actions">
                @if(Auth::user()->hasRole('Admin'))
                    <button @click="$dispatch('open-modal', 'create-subject')" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        <i class="fa-solid fa-plus mr-2"></i>
                        {{ __('New Subject') }}
                    </button>
                @endif
            </x-slot>

            <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Code</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Description</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Teacher</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Credits</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($subjects as $subject)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $subject->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $subject->code ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-slate-300 max-w-xs truncate">{{ \Illuminate\Support\Str::limit($subject->description ?? '—', 60) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $subject->teacher?->user->name ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $subject->credits ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                            <button @click="
                                $dispatch('view-subject', {
                                    name: '{{ $subject->name }}',
                                    code: '{{ $subject->code ?? '—' }}',
                                    description: '{{ $subject->description ?? '—' }}',
                                    teacher: '{{ $subject->teacher?->user->name ?? '—' }}',
                                    credits: '{{ $subject->credits ?? '—' }}'
                                })
                            " title="View" class="inline-flex items-center justify-center w-8 h-8 text-gray-400 dark:text-slate-500 hover:text-white hover:bg-gray-500 rounded-lg transition">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            @if(Auth::user()->hasRole('Admin'))
                                <button @click="
                                    $dispatch('edit-subject', {
                                        id: '{{ $subject->id }}',
                                        name: '{{ $subject->name }}',
                                        code: '{{ $subject->code }}',
                                        description: '{{ $subject->description }}',
                                        teacher_id: '{{ $subject->teacher_id }}',
                                        credits: '{{ $subject->credits }}'
                                    })
                                " title="Edit"
                                    class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button @click="$dispatch('set-confirmation', {
                                    action: '{{ route('subjects.destroy', $subject) }}',
                                    method: 'DELETE',
                                    title: 'Delete Subject',
                                    message: 'Delete this subject? This action cannot be undone.',
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
                        <td colspan="6" class="text-center py-12 text-gray-400 dark:text-slate-500">No subjects found.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>

    <x-modal name="create-subject" maxWidth="2xl" focusable>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ __('New Subject') }}</h2>
                <button @click="$dispatch('close-modal', 'create-subject')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('subjects.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Name') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                                </div>
                                <input type="text" name="name" value="{{ old('name') }}" placeholder="Subject name" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700" required>
                            </div>
                            @error('name')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Code') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 6h.008v.008H6V6z" /></svg>
                                </div>
                                <input type="text" name="code" value="{{ old('code') }}" placeholder="e.g. MATH-101" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700">
                            </div>
                            @error('code')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Description') }}</label>
                        <div class="relative">
                            <div class="absolute top-3 left-0 pl-3.5 flex items-start pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" /></svg>
                            </div>
                            <textarea name="description" rows="3" placeholder="Brief description of the subject" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700">{{ old('description') }}</textarea>
                        </div>
                        @error('description')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
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
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Credits') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <input type="number" name="credits" value="{{ old('credits') }}" placeholder="e.g. 3" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700">
                            </div>
                            @error('credits')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button @click="$dispatch('close-modal', 'create-subject')" type="button" class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">{{ __('Cancel') }}</button>
                        <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition shadow-sm">{{ __('Create subject') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="edit-subject" maxWidth="2xl" focusable>
        <div class="p-6" x-data="editSubjectData()" @edit-subject.window="load($event.detail)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ __('Edit Subject') }}</h2>
                <button @click="$dispatch('close-modal', 'edit-subject')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form method="POST" :action="`/subjects/${form.id}`">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Name') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                                </div>
                                <input type="text" name="name" x-model="form.name" placeholder="Subject name" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Code') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 6h.008v.008H6V6z" /></svg>
                                </div>
                                <input type="text" name="code" x-model="form.code" placeholder="e.g. MATH-101" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Description') }}</label>
                        <div class="relative">
                            <div class="absolute top-3 left-0 pl-3.5 flex items-start pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" /></svg>
                            </div>
                            <textarea name="description" rows="3" x-model="form.description" placeholder="Brief description of the subject" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700"></textarea>
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
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">{{ __('Credits') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <input type="number" name="credits" x-model="form.credits" placeholder="e.g. 3" class="block w-full pl-10 rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 dark:text-slate-200 dark:bg-slate-700">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button @click="$dispatch('close-modal', 'edit-subject')" type="button" class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">{{ __('Cancel') }}</button>
                        <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition shadow-sm">{{ __('Save changes') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="view-subject" maxWidth="2xl" focusable>
        <div class="p-6" x-data="viewSubjectData()" @view-subject.window="load($event.detail)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ __('View Subject') }}: <span x-text="data.name"></span></h2>
                <button @click="$dispatch('close-modal', 'view-subject')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Code</label>
                    <p class="text-sm text-gray-900 dark:text-slate-200" x-text="data.code"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Credits</label>
                    <p class="text-sm text-gray-900 dark:text-slate-200" x-text="data.credits"></p>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Description</label>
                    <p class="text-sm text-gray-900 dark:text-slate-200" x-text="data.description"></p>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Teacher</label>
                    <p class="text-sm text-gray-900 dark:text-slate-200" x-text="data.teacher"></p>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button @click="$dispatch('close-modal', 'view-subject')" type="button" class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">{{ __('Close') }}</button>
            </div>
        </div>
    </x-modal>

    <script>
    function editSubjectData() {
        return {
            form: { id: '', name: '', code: '', description: '', teacher_id: '', credits: '' },
            load(data) {
                this.form = { ...data };
                this.$dispatch('open-modal', 'edit-subject');
            }
        };
    }
    function viewSubjectData() {
        return {
            data: { name: '', code: '', description: '', teacher: '', credits: '' },
            load(data) {
                this.data = { ...data };
                this.$dispatch('open-modal', 'view-subject');
            }
        };
    }
    </script>
</x-app-layout>
