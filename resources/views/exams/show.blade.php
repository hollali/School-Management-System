@section('title', $exam->name)

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">{{ $exam->name }}</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Exam details and management</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('exams.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                    <i class="fa-solid fa-arrow-left"></i> Back
                </a>
                @if(Auth::user()->can('update', $exam))
                    <a href="{{ route('exams.edit', $exam) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 text-white text-sm font-semibold rounded-xl hover:bg-amber-600 transition shadow-sm">
                        <i class="fa-solid fa-pen-to-square"></i> Edit
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        {{-- Status Banner --}}
        <div class="rounded-xl p-4 @if($exam->is_published) bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 @else bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 @endif">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="fa-solid @if($exam->is_published) fa-check-circle text-emerald-600 @else fa-clock text-amber-600 @endif text-lg"></i>
                    <span class="text-sm font-semibold @if($exam->is_published) text-emerald-800 dark:text-emerald-200 @else text-amber-800 dark:text-amber-200 @endif">
                        {{ $exam->is_published ? 'Published' : 'Draft' }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-slate-400">|</span>
                    <span class="text-xs @if($exam->exam_mode === 'online') text-sky-600 @else text-gray-500 @endif font-medium">{{ ucfirst($exam->exam_mode) }} Exam</span>
                </div>
                <div class="flex items-center gap-2">
                    @if(!$exam->is_published && Auth::user()->can('update', $exam))
                        <form action="{{ route('exams.publish', $exam) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition shadow-sm">
                                <i class="fa-solid fa-check"></i> Publish
                            </button>
                        </form>
                    @endif
                    @if($exam->is_published && !$exam->results_published && Auth::user()->can('update', $exam))
                        <form action="{{ route('exams.results.publish', $exam) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 text-white text-sm font-semibold rounded-xl hover:bg-violet-700 transition shadow-sm">
                                <i class="fa-solid fa-chart-simple"></i> Publish Results
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Details Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <h3 class="text-base font-bold text-gray-900 dark:text-slate-200 mb-4">Exam Details</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Subject</p>
                            <p class="text-sm text-gray-900 dark:text-slate-200 mt-1">{{ $exam->subject?->name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Class</p>
                            <p class="text-sm text-gray-900 dark:text-slate-200 mt-1">{{ $exam->class?->name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Type</p>
                            <p class="text-sm text-gray-900 dark:text-slate-200 mt-1">{{ $exam->type ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Duration</p>
                            <p class="text-sm text-gray-900 dark:text-slate-200 mt-1">{{ $exam->duration_minutes ? $exam->duration_minutes . ' min' : '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Total Marks</p>
                            <p class="text-sm text-gray-900 dark:text-slate-200 mt-1">{{ $exam->total_marks ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Pass Mark</p>
                            <p class="text-sm text-gray-900 dark:text-slate-200 mt-1">{{ $exam->pass_mark ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Start Date</p>
                            <p class="text-sm text-gray-900 dark:text-slate-200 mt-1">{{ $exam->start_date ? \Carbon\Carbon::parse($exam->start_date)->format('M d, Y H:i') : '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">End Date</p>
                            <p class="text-sm text-gray-900 dark:text-slate-200 mt-1">{{ $exam->end_date ? \Carbon\Carbon::parse($exam->end_date)->format('M d, Y H:i') : '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Term</p>
                            <p class="text-sm text-gray-900 dark:text-slate-200 mt-1">{{ $exam->academicTerm?->name ?? $exam->term ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Academic Year</p>
                            <p class="text-sm text-gray-900 dark:text-slate-200 mt-1">{{ $exam->academic_year ?? '—' }}</p>
                        </div>
                    </div>

                    @if($exam->description)
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-slate-700">
                            <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-2">Description</p>
                            <p class="text-sm text-gray-700 dark:text-slate-300">{{ $exam->description }}</p>
                        </div>
                    @endif

                    @if($exam->instructions)
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-slate-700">
                            <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-2">Instructions</p>
                            <p class="text-sm text-gray-700 dark:text-slate-300 whitespace-pre-wrap">{{ $exam->instructions }}</p>
                        </div>
                    @endif
                </div>

                {{-- Questions --}}
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-gray-900 dark:text-slate-200">Questions ({{ $exam->questions->count() }})</h3>
                        @if(Auth::user()->can('update', $exam))
                            <button @click="$dispatch('open-modal', 'add-questions')" class="inline-flex items-center gap-2 px-3 py-1.5 bg-sky-600 text-white text-xs font-semibold rounded-lg hover:bg-sky-700 transition">
                                <i class="fa-solid fa-plus"></i> Add Questions
                            </button>
                        @endif
                    </div>

                    @if($exam->questions->count() > 0)
                        <div class="space-y-3">
                            @foreach($exam->questions as $index => $question)
                                <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                                    <span class="text-xs font-bold text-gray-400 dark:text-slate-500 mt-0.5 min-w-[20px]">{{ $index + 1 }}.</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-slate-200">{{ $question->question_text }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-xs px-2 py-0.5 rounded-full bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                                            <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300">{{ $question->pivot->marks ?? $question->default_marks }} marks</span>
                                            <span class="text-xs text-gray-400 dark:text-slate-500">{{ ucfirst($question->difficulty) }}</span>
                                        </div>
                                    </div>
                                    @if(Auth::user()->can('update', $exam))
                                        <form action="{{ route('exams.questions.remove', [$exam, $question->id]) }}" method="POST" class="shrink-0">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-600 transition" title="Remove">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fa-solid fa-file-circle-question text-3xl text-gray-200 dark:text-slate-600 mb-2"></i>
                            <p class="text-sm text-gray-400 dark:text-slate-500">No questions added yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <h3 class="text-base font-bold text-gray-900 dark:text-slate-200 mb-4">Security Settings</h3>
                    <div class="space-y-2">
                        @foreach(['shuffle_questions' => 'Shuffle Questions', 'shuffle_options' => 'Shuffle Options', 'negative_marking' => 'Negative Marking', 'fullscreen_required' => 'Require Fullscreen', 'tab_switch_detection' => 'Tab Switch Detection', 'copy_paste_disabled' => 'Disable Copy/Paste', 'show_results_immediately' => 'Show Results Immediately'] as $field => $label)
                            <div class="flex items-center justify-between py-1.5">
                                <span class="text-sm text-gray-600 dark:text-slate-400">{{ $label }}</span>
                                <span class="text-sm @if($exam->$field) text-emerald-600 @else text-gray-400 @endif">
                                    <i class="fa-solid @if($exam->$field) fa-check-circle @else fa-circle @endif"></i>
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                    <h3 class="text-base font-bold text-gray-900 dark:text-slate-200 mb-4">Schedules</h3>
                    @if($exam->schedules->count() > 0)
                        <div class="space-y-2">
                            @foreach($exam->schedules as $schedule)
                                <div class="p-2 bg-gray-50 dark:bg-slate-700/50 rounded-lg text-sm">
                                    <p class="font-medium text-gray-900 dark:text-slate-200">{{ $schedule->class?->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">{{ $schedule->exam_date?->format('M d, Y') }} | {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400 dark:text-slate-500">No schedules set.</p>
                    @endif
                </div>

                @if(Auth::user()->hasRole('Teacher') || Auth::user()->hasRole('Admin'))
                    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                        <h3 class="text-base font-bold text-gray-900 dark:text-slate-200 mb-4">Attempts ({{ $attempts?->count() ?? 0 }})</h3>
                        @if($attempts && $attempts->count() > 0)
                            <div class="space-y-2">
                                @foreach($attempts as $attempt)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-slate-700/50 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-slate-200">{{ $attempt->student->user->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-slate-400">
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-medium
                                                    @if($attempt->status === 'graded') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300
                                                    @elseif($attempt->status === 'submitted') bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300
                                                    @else bg-gray-100 text-gray-600 dark:bg-slate-600 dark:text-slate-300 @endif">
                                                    {{ ucfirst($attempt->status) }}
                                                </span>
                                            </p>
                                        </div>
                                        @if($attempt->status === 'graded')
                                            <span class="text-sm font-bold @if($attempt->result_status === 'pass') text-emerald-600 @else text-red-600 @endif">{{ $attempt->percentage_score }}%</span>
                                        @elseif($attempt->status === 'submitted')
                                            <a href="{{ route('grading.grade', $attempt) }}" class="text-xs text-sky-600 hover:text-sky-800 font-medium">Grade</a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-400 dark:text-slate-500">No attempts yet.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Add Questions Modal --}}
    <x-modal name="add-questions" maxWidth="4xl" focusable>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Add Questions</h2>
                <button @click="$dispatch('close-modal', 'add-questions')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('exams.questions.add', $exam) }}" method="POST">
                @csrf
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @forelse($questionBank as $question)
                        <label class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700 transition">
                            <input type="checkbox" name="question_ids[]" value="{{ $question->id }}" class="mt-1 rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-slate-200">{{ $question->question_text }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                                    <span class="text-xs text-gray-400 dark:text-slate-500">{{ $question->default_marks }} marks</span>
                                    <span class="text-xs text-gray-400 dark:text-slate-500">{{ ucfirst($question->difficulty) }}</span>
                                </div>
                            </div>
                            <div class="w-20 shrink-0">
                                <input type="number" name="marks[{{ $question->id }}]" placeholder="Marks" value="{{ $question->default_marks }}" step="0.01" min="0"
                                    class="block w-full rounded-lg border-gray-200 dark:border-slate-600 text-xs py-1.5 px-2 dark:bg-slate-700 dark:text-slate-200">
                            </div>
                        </label>
                    @empty
                        <div class="text-center py-8">
                            <i class="fa-solid fa-database text-3xl text-gray-200 dark:text-slate-600 mb-2"></i>
                            <p class="text-sm text-gray-400 dark:text-slate-500">No questions available for this subject. <a href="{{ route('questions.create') }}" class="text-sky-600 hover:underline">Create one</a></p>
                        </div>
                    @endforelse
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button @click="$dispatch('close-modal', 'add-questions')" type="button" class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">Cancel</button>
                    <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                        <i class="fa-solid fa-plus mr-2"></i> Add Selected
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</x-app-layout>
