<x-app-layout>
    @section('title', 'Results')

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Results</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Manage examination results and grades</p>
        </div>
    </x-slot>

    <div class="py-6">
        <x-data-table title="Results" :data="$results" searchable="true" searchPlaceholder="Search results..." searchValue="{{ request('search') }}" searchRoute="{{ route('results.index') }}">
            <x-slot name="actions">
                @if(Auth::user()->hasRole('Teacher'))
                    <button @click="$dispatch('open-modal', 'create-result')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        <i class="fa-solid fa-plus"></i>
                        New Result
                    </button>
                @endif
            </x-slot>
            <x-slot name="filters">
                <select name="exam_id" @change="const p=new URLSearchParams(location.search);p.set('exam_id',$event.target.value);p.delete('page');window.location='{{ route('results.index') }}?'+p"
                    class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 pl-3 pr-8 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">All Exams</option>
                    @foreach($exams as $exam)
                        <option value="{{ $exam->id }}" @selected(request('exam_id') == $exam->id)>{{ $exam->name }}</option>
                    @endforeach
                </select>
                <select name="subject_id" @change="const p=new URLSearchParams(location.search);p.set('subject_id',$event.target.value);p.delete('page');window.location='{{ route('results.index') }}?'+p"
                    class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 pl-3 pr-8 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected(request('subject_id') == $subject->id)>{{ $subject->name }}</option>
                    @endforeach
                </select>
                <select name="student_id" @change="const p=new URLSearchParams(location.search);p.set('student_id',$event.target.value);p.delete('page');window.location='{{ route('results.index') }}?'+p"
                    class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 pl-3 pr-8 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">All Students</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" @selected(request('student_id') == $student->id)>{{ $student->user->name }}</option>
                    @endforeach
                </select>
            </x-slot>
            <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Student</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Exam</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Subject</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Score</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Grade</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($results as $result)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-slate-200 font-medium">{{ $result->student->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $result->exam->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $result->subject->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $result->score }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $score = $result->score;
                                $gradeColor = 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200';
                                if ($score < 40) $gradeColor = 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200';
                                elseif ($score < 60) $gradeColor = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $gradeColor }}">
                                {{ $result->grade->name ?? '—' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                            <button @click="
                                $dispatch('view-result', @js([
                                    'student' => $result->student->user->name,
                                    'subject' => $result->subject->name,
                                    'exam' => $result->exam->name,
                                    'score' => $result->score,
                                    'grade' => $result->grade->name ?? '—',
                                    'remarks' => $result->remarks ?? '—',
                                ]));
                                $dispatch('open-modal', 'view-result');
                            " class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition" title="View">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            @if(Auth::user()->hasRole('Teacher'))
                                <button @click="
                                    $dispatch('edit-result', @js([
                                        'id' => $result->id,
                                        'student_id' => (string) $result->student_id,
                                        'subject_id' => (string) $result->subject_id,
                                        'exam_id' => (string) $result->exam_id,
                                        'score' => (string) $result->score,
                                        'remarks' => $result->remarks ?? '',
                                    ]));
                                    $dispatch('open-modal', 'edit-result');
                                " class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button @click="$dispatch('set-confirmation', {
                                    action: '{{ route('results.destroy', $result) }}',
                                    method: 'DELETE',
                                    title: 'Delete Result',
                                    message: 'Delete this result? This action cannot be undone.',
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
                        <td colspan="6" class="text-center py-12 text-gray-400 dark:text-slate-500">No results found.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>

    <x-modal name="create-result" maxWidth="2xl" focusable>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ __('New Result') }}</h2>
                <button @click="$dispatch('close-modal', 'create-result')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-white transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('results.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Student</label>
                            <select name="student_id"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200" required>
                                <option value="">Select student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>{{ $student->user->name }}</option>
                                @endforeach
                            </select>
                            @error('student_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Subject</label>
                            <select name="subject_id"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200" required>
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
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Exam</label>
                            <select name="exam_id"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200" required>
                                <option value="">Select exam</option>
                                @foreach($exams as $exam)
                                    <option value="{{ $exam->id }}" {{ old('exam_id') == $exam->id ? 'selected' : '' }}>{{ $exam->name }}</option>
                                @endforeach
                            </select>
                            @error('exam_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Score</label>
                            <input type="number" name="score" value="{{ old('score') }}" step="0.01"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200" required>
                            @error('score')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Remarks</label>
                        <textarea name="remarks" rows="3"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">{{ old('remarks') }}</textarea>
                        @error('remarks')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex justify-end gap-3">
                        <button @click="$dispatch('close-modal', 'create-result')" type="button"
                            class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">Cancel</button>
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">Create result</button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="edit-result" maxWidth="2xl" focusable>
        <div class="p-6" x-data="editResultData()" @edit-result.window="load($event.detail)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ __('Edit Result') }}</h2>
                <button @click="$dispatch('close-modal', 'edit-result')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-white transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form method="POST" :action="`/results/${form.id}`">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 gap-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Student</label>
                            <select name="student_id" x-model="form.student_id"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200" required>
                                <option value="">Select student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->user->name }}</option>
                                @endforeach
                            </select>
                            @error('student_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Subject</label>
                            <select name="subject_id" x-model="form.subject_id"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200" required>
                                <option value="">Select subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                            @error('subject_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Exam</label>
                            <select name="exam_id" x-model="form.exam_id"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 bg-white dark:bg-slate-700 dark:text-slate-200" required>
                                <option value="">Select exam</option>
                                @foreach($exams as $exam)
                                    <option value="{{ $exam->id }}">{{ $exam->name }}</option>
                                @endforeach
                            </select>
                            @error('exam_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Score</label>
                            <input type="number" name="score" x-model="form.score" step="0.01"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200" required>
                            @error('score')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Remarks</label>
                        <textarea name="remarks" rows="3" x-model="form.remarks"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200"></textarea>
                        @error('remarks')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex justify-end gap-3">
                        <button @click="$dispatch('close-modal', 'edit-result')" type="button"
                            class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">Cancel</button>
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="view-result" maxWidth="lg" focusable>
        <div class="p-6" x-data="viewResultData()" @view-result.window="load($event.detail)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Result Details</h2>
                <button @click="$dispatch('close-modal', 'view-result')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-white">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-4">
                    <p class="text-sm text-gray-500 dark:text-slate-400">Student</p>
                    <p class="text-sm text-gray-900 dark:text-slate-200 font-medium mt-1" x-text="data.student"></p>
                </div>
                <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-4">
                    <p class="text-sm text-gray-500 dark:text-slate-400">Subject</p>
                    <p class="text-sm text-gray-900 dark:text-slate-200 font-medium mt-1" x-text="data.subject"></p>
                </div>
                <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-4">
                    <p class="text-sm text-gray-500 dark:text-slate-400">Exam</p>
                    <p class="text-sm text-gray-900 dark:text-slate-200 font-medium mt-1" x-text="data.exam"></p>
                </div>
                <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-4">
                    <p class="text-sm text-gray-500 dark:text-slate-400">Score</p>
                    <p class="text-sm text-gray-900 dark:text-slate-200 font-medium mt-1" x-text="data.score"></p>
                </div>
                <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-4">
                    <p class="text-sm text-gray-500 dark:text-slate-400">Grade</p>
                    <p class="text-sm text-gray-900 dark:text-slate-200 font-medium mt-1" x-text="data.grade"></p>
                </div>
            </div>
            <div class="mt-4 bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-4" x-show="data.remarks && data.remarks !== '—'">
                <p class="text-sm text-gray-500 dark:text-slate-400">Remarks</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-slate-200" x-text="data.remarks"></p>
            </div>
        </div>
    </x-modal>

    <script>
    function editResultData() {
        return {
            form: { id: '', student_id: '', subject_id: '', exam_id: '', score: '', remarks: '' },
            load(data) {
                this.form = { ...data };
            }
        };
    }
    function viewResultData() {
        return {
            data: { student: '', subject: '', exam: '', score: '', grade: '', remarks: '' },
            load(data) {
                this.data = { ...data };
            }
        };
    }
    </script>
</x-app-layout>
