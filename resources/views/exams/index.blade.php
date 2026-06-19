<x-app-layout>
    @section('title', 'Exams')

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Exams</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Manage exam schedules and records</p>
        </div>
    </x-slot>

    <div class="py-6">
        <x-data-table title="Exams" :data="$exams" searchable="true" searchPlaceholder="Search exams..." searchValue="{{ request('search') }}" searchRoute="{{ route('exams.index') }}">
            @if(Auth::user()->hasRole('Teacher'))
            <x-slot name="actions">
                <button @click="$dispatch('open-modal', 'create-exam')" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Exam
                </button>
            </x-slot>
            @endif

            <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Title</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Subject</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Type</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Duration</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Max Score</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($exams as $exam)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-slate-200">{{ $exam->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $exam->subject->name ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $exam->type ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $exam->exam_date ? \Carbon\Carbon::parse($exam->exam_date)->format('M d, Y') : '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $exam->duration ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $exam->max_score ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                        <button @click="
                            $dispatch('view-exam', {
                                name: '{{ $exam->name }}',
                                type: '{{ $exam->type ?? '—' }}',
                                exam_date: '{{ $exam->exam_date ? \Carbon\Carbon::parse($exam->exam_date)->format('M d, Y') : '—' }}',
                                term: '{{ $exam->term ?? '—' }}',
                                academic_year: '{{ $exam->academic_year ?? '—' }}'
                            })
                        " title="View" class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        @if(Auth::user()->hasRole('Teacher'))
                        <button @click="
                            $dispatch('edit-exam', {
                                id: '{{ $exam->id }}',
                                name: '{{ $exam->name }}',
                                type: '{{ $exam->type }}',
                                exam_date: '{{ $exam->exam_date ? \Carbon\Carbon::parse($exam->exam_date)->format('Y-m-d') : '' }}',
                                term: '{{ $exam->term }}',
                                academic_year: '{{ $exam->academic_year }}'
                            })
                        " title="Edit" class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                        <button @click="$dispatch('set-confirmation', {
                            action: '{{ route('exams.destroy', $exam) }}',
                            method: 'DELETE',
                            title: 'Delete Exam',
                            message: 'Delete this exam? This action cannot be undone.',
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
                    <td colspan="7" class="text-center py-12 text-gray-400 dark:text-slate-500">No exams found.</td>
                </tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>

    <x-modal name="create-exam" maxWidth="2xl" focusable>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ __('New Exam') }}</h2>
                <button @click="$dispatch('close-modal', 'create-exam')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('exams.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700"
                            required>
                        @error('name')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Type</label>
                            <input type="text" name="type" value="{{ old('type') }}"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                            @error('type')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Date</label>
                            <input type="date" name="exam_date" value="{{ old('exam_date') }}"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                            @error('exam_date')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Term</label>
                            <input type="text" name="term" value="{{ old('term') }}"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                            @error('term')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Academic Year</label>
                            <input type="text" name="academic_year" value="{{ old('academic_year') }}"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                            @error('academic_year')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button @click="$dispatch('close-modal', 'create-exam')" type="button"
                            class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">Cancel</button>
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">Create exam</button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="edit-exam" maxWidth="2xl" focusable>
        <div class="p-6" x-data="editExamData()" @edit-exam.window="load($event.detail)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ __('Edit Exam') }}</h2>
                <button @click="$dispatch('close-modal', 'edit-exam')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form method="POST" :action="`/exams/${form.id}`">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Name</label>
                        <input type="text" name="name" x-model="form.name"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700"
                            required>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Type</label>
                            <input type="text" name="type" x-model="form.type"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Date</label>
                            <input type="date" name="exam_date" x-model="form.exam_date"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Term</label>
                            <input type="text" name="term" x-model="form.term"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Academic Year</label>
                            <input type="text" name="academic_year" x-model="form.academic_year"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:text-slate-200 dark:bg-slate-700">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button @click="$dispatch('close-modal', 'edit-exam')" type="button"
                            class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">Cancel</button>
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="view-exam" maxWidth="2xl" focusable>
        <div class="p-6" x-data="viewExamData()" @view-exam.window="load($event.detail)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ __('View Exam') }}: <span x-text="data.name"></span></h2>
                <button @click="$dispatch('close-modal', 'view-exam')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Type</label>
                    <p class="text-sm text-gray-900 dark:text-slate-200" x-text="data.type"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Date</label>
                    <p class="text-sm text-gray-900 dark:text-slate-200" x-text="data.exam_date"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Term</label>
                    <p class="text-sm text-gray-900 dark:text-slate-200" x-text="data.term"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1">Academic Year</label>
                    <p class="text-sm text-gray-900 dark:text-slate-200" x-text="data.academic_year"></p>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button @click="$dispatch('close-modal', 'view-exam')" type="button" class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">{{ __('Close') }}</button>
            </div>
        </div>
    </x-modal>

    <script>
    function editExamData() {
        return {
            form: { id: '', name: '', type: '', exam_date: '', term: '', academic_year: '' },
            load(data) {
                this.form = { ...data };
                this.$dispatch('open-modal', 'edit-exam');
            }
        };
    }
    function viewExamData() {
        return {
            data: { name: '', type: '', exam_date: '', term: '', academic_year: '' },
            load(data) {
                this.data = { ...data };
                this.$dispatch('open-modal', 'view-exam');
            }
        };
    }
    </script>
</x-app-layout>
