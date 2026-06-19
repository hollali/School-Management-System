<x-app-layout>
    @section('title', 'Assignments')

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Assignments</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Manage class assignments and deadlines</p>
        </div>
    </x-slot>

    <div class="py-6">
        <x-data-table title="Assignments" :data="$assignments" searchable="true" searchPlaceholder="Search assignments..." searchValue="{{ request('search') }}" searchRoute="{{ route('assignments.index') }}">
            <x-slot name="actions">
                @if(Auth::user()->hasRole('Teacher'))
                    <button @click="$dispatch('open-modal', 'create-assignment')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        <i class="fa-solid fa-plus"></i>
                        New Assignment
                    </button>
                @endif
            </x-slot>
            <x-slot name="filters">
                <select name="class_id" @change="const p=new URLSearchParams(location.search);p.set('class_id',$event.target.value);p.delete('page');window.location='{{ route('assignments.index') }}?'+p"
                    class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 pl-3 pr-8 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->name }}</option>
                    @endforeach
                </select>
                <select name="subject_id" @change="const p=new URLSearchParams(location.search);p.set('subject_id',$event.target.value);p.delete('page');window.location='{{ route('assignments.index') }}?'+p"
                    class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 pl-3 pr-8 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected(request('subject_id') == $subject->id)>{{ $subject->name }}</option>
                    @endforeach
                </select>
            </x-slot>
            <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Title</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Class</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Subject</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Due Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($assignments as $assignment)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-slate-200 font-medium">{{ $assignment->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $assignment->schoolClass?->name ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $assignment->subject?->name ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($assignment->due_date)
                                @php
                                    $isOverdue = $assignment->due_date->isPast();
                                    $daysLeft = now()->diffInDays($assignment->due_date, false);
                                @endphp
                                <span class="{{ $isOverdue ? 'text-red-500' : 'text-gray-600' }}">
                                    {{ $assignment->due_date->format('M d, Y') }}
                                    @if($daysLeft > 0 && !$isOverdue)
                                        <span class="text-xs text-gray-400 dark:text-slate-500">({{ round($daysLeft) }}d left)</span>
                                    @elseif($isOverdue)
                                        <span class="text-xs text-red-400">(overdue)</span>
                                    @endif
                                </span>
                            @else
                                <span class="text-gray-400 dark:text-slate-500">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $status = 'Active';
                                $statusColor = 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-200';
                                if (!$assignment->due_date) {
                                    $status = 'No Due Date';
                                    $statusColor = 'bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-slate-200';
                                } elseif ($assignment->due_date->isPast()) {
                                    $status = 'Overdue';
                                    $statusColor = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-200';
                                } elseif ($assignment->due_date->isToday()) {
                                    $status = 'Due Today';
                                    $statusColor = 'bg-amber-100 text-amber-700 dark:bg-yellow-900/30 dark:text-yellow-200';
                                } elseif ($assignment->due_date->diffInDays(now()) <= 3) {
                                    $status = 'Due Soon';
                                    $statusColor = 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-200';
                                }
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">{{ $status }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                            <button @click="
                                $dispatch('view-assignment', @js([
                                    'id' => $assignment->id,
                                    'title' => $assignment->title,
                                    'class' => $assignment->schoolClass?->name ?? '—',
                                    'subject' => $assignment->subject?->name ?? '—',
                                    'teacher' => $assignment->teacher?->user?->name ?? '—',
                                    'due_date' => $assignment->due_date?->format('M d, Y') ?? '—',
                                    'description' => $assignment->description ?? '—',
                                ]));
                                $dispatch('open-modal', 'view-assignment');
                            " class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition" title="View">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            @if(Auth::user()->hasRole('Student'))
                                <a href="{{ route('assignments.show', $assignment) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 text-emerald-600 hover:text-white hover:bg-emerald-600 rounded-lg transition" title="Submit">
                                    <i class="fa-solid fa-upload"></i>
                                </a>
                            @endif
                            @if(Auth::user()->hasRole('Teacher'))
                                <button @click="
                                    $dispatch('edit-assignment', @js([
                                        'id' => $assignment->id,
                                        'class_id' => (string) $assignment->class_id,
                                        'subject_id' => (string) $assignment->subject_id,
                                        'title' => $assignment->title,
                                        'description' => $assignment->description,
                                        'due_date' => $assignment->due_date?->format('Y-m-d') ?? '',
                                    ]));
                                    $dispatch('open-modal', 'edit-assignment');
                                " class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button @click="$dispatch('set-confirmation', {
                                    action: '{{ route('assignments.destroy', $assignment) }}',
                                    method: 'DELETE',
                                    title: 'Delete Assignment',
                                    message: 'Delete this assignment? This action cannot be undone.',
                                    confirmLabel: 'Delete',
                                    confirmClass: 'bg-red-600 hover:bg-red-700'
                                })" class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:text-white hover:bg-red-500 rounded-lg transition" title="Delete">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-gray-400 dark:text-slate-500">No assignments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>

    <x-modal name="create-assignment" maxWidth="2xl" focusable>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ __('New Assignment') }}</h2>
                <button @click="$dispatch('close-modal', 'create-assignment')" type="button" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('assignments.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Class</label>
                            <select name="class_id"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                @endforeach
                            </select>
                            @error('class_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Subject</label>
                            <select name="subject_id"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                @endforeach
                            </select>
                            @error('subject_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Title</label>
                        <input type="text" name="title" value="{{ old('title') }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4" required>
                        @error('title')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Description</label>
                        <textarea name="description" rows="5"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">{{ old('description') }}</textarea>
                        @error('description')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Due Date</label>
                        <input type="date" name="due_date" value="{{ old('due_date') }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                        @error('due_date')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex items-center justify-end gap-3">
                        <button @click="$dispatch('close-modal', 'create-assignment')" type="button"
                            class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                            Create assignment
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="edit-assignment" maxWidth="2xl" focusable>
        <div class="p-6" x-data="editAssignmentData()" @edit-assignment.window="load($event.detail)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ __('Edit Assignment') }}</h2>
                <button @click="$dispatch('close-modal', 'edit-assignment')" type="button" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form method="POST" :action="`/assignments/${form.id}`">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 gap-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Class</label>
                            <select name="class_id" x-model="form.class_id"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                            @error('class_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Subject</label>
                            <select name="subject_id" x-model="form.subject_id"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                            @error('subject_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Title</label>
                        <input type="text" name="title" x-model="form.title"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4" required>
                        @error('title')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Description</label>
                        <textarea name="description" rows="5" x-model="form.description"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4"></textarea>
                        @error('description')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Due Date</label>
                        <input type="date" name="due_date" x-model="form.due_date"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4">
                        @error('due_date')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex items-center justify-end gap-3">
                        <button @click="$dispatch('close-modal', 'edit-assignment')" type="button"
                            class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                            Update assignment
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="view-assignment" maxWidth="2xl" focusable>
        <div class="p-6" x-data="viewAssignmentData()" @view-assignment.window="load($event.detail)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200" x-text="data.title"></h2>
                <button @click="$dispatch('close-modal', 'view-assignment')" type="button" class="text-gray-400 hover:text-gray-600 dark:text-slate-500 dark:hover:text-slate-300">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-4">
                    <p class="text-sm text-gray-500 dark:text-slate-400">Class</p>
                    <p class="text-sm text-gray-900 dark:text-slate-200 font-medium mt-1" x-text="data.class"></p>
                </div>
                <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-4">
                    <p class="text-sm text-gray-500 dark:text-slate-400">Subject</p>
                    <p class="text-sm text-gray-900 dark:text-slate-200 font-medium mt-1" x-text="data.subject"></p>
                </div>
                <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-4">
                    <p class="text-sm text-gray-500 dark:text-slate-400">Teacher</p>
                    <p class="text-sm text-gray-900 dark:text-slate-200 font-medium mt-1" x-text="data.teacher"></p>
                </div>
                <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-4">
                    <p class="text-sm text-gray-500 dark:text-slate-400">Due Date</p>
                    <p class="text-sm text-gray-900 dark:text-slate-200 font-medium mt-1" x-text="data.due_date"></p>
                </div>
            </div>
            <div class="mt-4 bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-4" x-show="data.description">
                <p class="text-sm text-gray-500 dark:text-slate-400">Description</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-slate-200 whitespace-pre-wrap" x-text="data.description"></p>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <a :href="`/assignments/${data.id}`" target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                    Full Details
                </a>
                @if(Auth::user()->hasRole('Student'))
                    <a :href="`/assignments/${data.id}`"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-semibold rounded-xl hover:from-emerald-700 hover:to-teal-700 transition shadow-sm">
                        <i class="fa-solid fa-upload"></i> Submit Assignment
                    </a>
                @endif
            </div>
        </div>
    </x-modal>

    <script>
    function editAssignmentData() {
        return {
            form: { id: '', class_id: '', subject_id: '', title: '', description: '', due_date: '' },
            load(data) {
                this.form = { ...data };
            }
        };
    }
    function viewAssignmentData() {
        return {
            data: { id: '', title: '', class: '', subject: '', teacher: '', due_date: '', description: '' },
            load(data) {
                this.data = { ...data };
            }
        };
    }
    </script>
</x-app-layout>
