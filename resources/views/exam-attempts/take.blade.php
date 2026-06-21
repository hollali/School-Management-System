@section('title', $exam->name)

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark:bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $exam->name }} - Exam</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script>if(localStorage.getItem('darkMode')==='true')document.documentElement.classList.add('dark')</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .exam-container { max-width: 1000px; margin: 0 auto; }
        .question-card { transition: all 0.2s ease; }
        .question-card.active { border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14,165,233,0.15); }
        .nav-btn { transition: all 0.15s ease; }
        .nav-btn.active { background: #0ea5e9; color: white; }
        .nav-btn.answered { background: #10b981; color: white; }
        .nav-btn.flagged { background: #f59e0b; color: white; }
        .nav-btn.current { ring: 2px solid #0ea5e9; }
        .timer-critical { animation: pulse 1s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-slate-900">
    {{-- Top Bar --}}
    <header class="sticky top-0 z-50 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 shadow-sm">
        <div class="exam-container px-4 py-3 flex items-center justify-between">
            <div class="min-w-0 flex-1">
                <h1 class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $exam->name }}</h1>
                <p class="text-xs text-gray-500 dark:text-slate-400">{{ $exam->subject?->name }}</p>
            </div>
            <div class="flex items-center gap-4 shrink-0">
                <div class="text-right">
                    <p class="text-xs text-gray-500 dark:text-slate-400">Time Remaining</p>
                    <p id="timer" class="text-lg font-bold font-mono tabular-nums {{ $exam->duration_minutes <= 10 ? 'text-red-600 timer-critical' : 'text-gray-900 dark:text-white' }}"
                       data-duration="{{ $exam->duration_minutes }}" data-started="{{ $attempt->started_at }}">
                        --:--
                    </p>
                </div>
                <button id="submit-exam-btn" class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition shadow-sm">
                    <i class="fa-solid fa-check mr-1"></i> Submit
                </button>
            </div>
        </div>
    </header>

    <div class="exam-container px-4 py-6">
        <div class="flex gap-6">
            {{-- Question Navigation Panel --}}
            <div class="hidden lg:block w-56 shrink-0">
                <div class="sticky top-24 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-4">
                    <p class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-3">Questions</p>
                    <div class="grid grid-cols-5 gap-1.5 mb-4" id="question-nav">
                        @foreach($exam->questions as $index => $question)
                            <button type="button" data-q="{{ $index }}"
                                class="nav-btn w-8 h-8 rounded-lg text-xs font-semibold border border-gray-200 dark:border-slate-600 text-gray-600 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>
                    <div class="space-y-1.5 text-xs text-gray-500 dark:text-slate-400">
                        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-emerald-500"></span> Answered</div>
                        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded bg-amber-500"></span> Flagged</div>
                        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded border-2 border-gray-300 dark:border-slate-600"></span> Unanswered</div>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="flex-1 min-w-0">
                <form id="exam-form" method="POST" action="{{ route('student.exams.submit', $exam) }}">
                    @csrf

                    @foreach($exam->questions as $index => $question)
                        <div class="question-card bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 mb-4" data-q="{{ $index }}" id="q-{{ $index }}">
                            <div class="flex items-start gap-3 mb-4">
                                <span class="text-sm font-bold text-sky-600 dark:text-sky-400 shrink-0">Q{{ $index + 1 }}.</span>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-slate-200">{{ $question->question_text }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-300">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                                        <span class="text-xs text-gray-400 dark:text-slate-500">{{ $question->pivot->marks ?? $question->default_marks }} marks</span>
                                    </div>
                                </div>
                                <button type="button" class="flag-btn text-gray-300 hover:text-amber-500 transition shrink-0" data-q="{{ $index }}" title="Flag for review">
                                    <i class="fa-regular fa-flag text-lg"></i>
                                </button>
                            </div>

                            <div class="space-y-2">
                                @if(in_array($question->question_type, ['mcq', 'true_false']))
                                    @php $inputType = $question->question_type === 'true_false' ? 'radio' : 'radio'; @endphp
                                    @foreach($question->options as $option)
                                        <label class="option-label flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-slate-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700/50 transition has-[:checked]:bg-sky-50 has-[:checked]:border-sky-300 dark:has-[:checked]:bg-sky-900/20 dark:has-[:checked]:border-sky-700">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}"
                                                class="border-gray-300 text-sky-600 focus:ring-sky-500"
                                                onchange="markAnswered({{ $index }})">
                                            <span class="text-sm text-gray-700 dark:text-slate-300">{{ $option->option_text }}</span>
                                        </label>
                                    @endforeach
                                @elseif($question->question_type === 'multi_select')
                                    @foreach($question->options as $option)
                                        <label class="option-label flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-slate-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700/50 transition has-[:checked]:bg-sky-50 has-[:checked]:border-sky-300 dark:has-[:checked]:bg-sky-900/20 dark:has-[:checked]:border-sky-700">
                                            <input type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $option->id }}"
                                                class="rounded border-gray-300 text-sky-600 focus:ring-sky-500"
                                                onchange="markAnswered({{ $index }})">
                                            <span class="text-sm text-gray-700 dark:text-slate-300">{{ $option->option_text }}</span>
                                        </label>
                                    @endforeach
                                @elseif(in_array($question->question_type, ['short_answer', 'fill_blank', 'numeric']))
                                    <input type="text" name="answers[{{ $question->id }}]"
                                        placeholder="Your answer..."
                                        class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200"
                                        onchange="markAnswered({{ $index }})">
                                @elseif($question->question_type === 'essay')
                                    <textarea name="answers[{{ $question->id }}]" rows="5"
                                        placeholder="Write your answer..."
                                        class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200"
                                        onchange="markAnswered({{ $index }})"></textarea>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <div class="flex items-center justify-between mt-6">
                        <p class="text-sm text-gray-500 dark:text-slate-400">
                            <span id="answered-count">0</span>/{{ $exam->questions->count() }} answered
                        </p>
                        <button type="submit" class="px-6 py-3 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition shadow-lg">
                            <i class="fa-solid fa-check mr-2"></i> Submit Exam
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Submit Confirmation Modal --}}
    <div id="submit-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-gray-900/60" onclick="closeSubmitModal()"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl p-6 max-w-md w-full relative">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Submit Exam?</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mb-4">You have <span id="modal-answered">0</span> of {{ $exam->questions->count() }} questions answered. Unanswered questions will be marked as incorrect.</p>
                <div class="flex justify-end gap-3">
                    <button onclick="closeSubmitModal()" class="px-5 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 transition">Review</button>
                    <button onclick="document.getElementById('exam-form').submit()" class="px-5 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition shadow-sm">Submit Now</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab Switch Warning --}}
    <div id="tab-warning" class="fixed inset-0 z-50 hidden bg-red-600/90 flex items-center justify-center">
        <div class="text-center text-white p-8">
            <i class="fa-solid fa-exclamation-triangle text-5xl mb-4"></i>
            <h3 class="text-2xl font-bold mb-2">Warning!</h3>
            <p class="text-lg">Tab switching is not allowed during the exam.</p>
            <p class="text-sm mt-2 opacity-75">This incident has been recorded.</p>
        </div>
    </div>

    <script>
        const questions = {{ $exam->questions->count() }};
        const duration = {{ $exam->duration_minutes }};
        const startedAt = new Date('{{ $attempt->started_at }}').getTime();
        const endTime = startedAt + duration * 60 * 1000;
        let answered = new Set();
        let flagged = new Set();
        let tabSwitches = 0;
        const maxTabSwitches = {{ $exam->tab_switch_detection ? 3 : 999 }};
        const disableCopyPaste = {{ $exam->copy_paste_disabled ? 'true' : 'false' }};
        const requireFullscreen = {{ $exam->fullscreen_required ? 'true' : 'false' }};
        const examId = {{ $exam->id }};
        const csrfToken = '{{ csrf_token() }}';

        // Timer
        function updateTimer() {
            const now = Date.now();
            const remaining = Math.max(0, endTime - now);
            const minutes = Math.floor(remaining / 60000);
            const seconds = Math.floor((remaining % 60000) / 1000);
            const timer = document.getElementById('timer');
            timer.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

            if (minutes < 10) timer.classList.add('text-red-600', 'timer-critical');

            if (remaining <= 0) {
                document.getElementById('exam-form').submit();
            }
        }
        setInterval(updateTimer, 1000);
        updateTimer();

        // Navigation
        function showQuestion(index) {
            document.querySelectorAll('.question-card').forEach((el, i) => {
                el.style.display = i === index ? 'block' : 'none';
            });
            document.querySelectorAll('.nav-btn').forEach((btn, i) => {
                btn.classList.remove('current');
                if (i === index) btn.classList.add('current');
            });
        }
        showQuestion(0);

        document.getElementById('question-nav').addEventListener('click', function(e) {
            if (e.target.classList.contains('nav-btn')) {
                showQuestion(parseInt(e.target.dataset.q));
            }
        });

        // Mark answered
        function markAnswered(index) {
            answered.add(index);
            const btn = document.querySelector(`.nav-btn[data-q="${index}"]`);
            if (btn) btn.classList.add('answered');
            document.getElementById('answered-count').textContent = answered.size;

            // Auto-save
            autoSave(index);
        }

        // Flag
        document.querySelectorAll('.flag-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const index = parseInt(this.dataset.q);
                const navBtn = document.querySelector(`.nav-btn[data-q="${index}"]`);
                if (flagged.has(index)) {
                    flagged.delete(index);
                    this.innerHTML = '<i class="fa-regular fa-flag text-lg"></i>';
                    if (navBtn && answered.has(index)) navBtn.className = 'nav-btn answered';
                    else if (navBtn) navBtn.className = 'nav-btn';
                } else {
                    flagged.add(index);
                    this.innerHTML = '<i class="fa-solid fa-flag text-lg text-amber-500"></i>';
                    if (navBtn) { navBtn.className = 'nav-btn flagged'; }
                }
            });
        });

        // Auto-save
        function autoSave(index) {
            const qCard = document.getElementById(`q-${index}`);
            if (!qCard) return;
            const inputs = qCard.querySelectorAll('input[type=radio]:checked, input[type=checkbox]:checked, input[type=text], textarea');
            const data = new FormData();
            data.append('_token', csrfToken);
            data.append('question_id', {{ $exam->questions->first()->id ?? 0 }});

            // We'll just save the whole form periodically
        }

        // Periodic auto-save
        setInterval(() => {
            const formData = new FormData(document.getElementById('exam-form'));
            formData.append('_token', csrfToken);
            fetch('{{ route('student.exams.save', $exam) }}', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).catch(() => {});
        }, 30000);

        // Submit confirmation
        document.getElementById('submit-exam-btn').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('modal-answered').textContent = answered.size;
            document.getElementById('submit-modal').classList.remove('hidden');
        });

        function closeSubmitModal() {
            document.getElementById('submit-modal').classList.add('hidden');
        }

        // Tab switch detection
        if ({{ $exam->tab_switch_detection ? 'true' : 'false' }}) {
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    tabSwitches++;
                    if (tabSwitches >= maxTabSwitches) {
                        document.getElementById('tab-warning').classList.remove('hidden');
                        setTimeout(() => { document.getElementById('exam-form').submit(); }, 3000);
                    }
                }
            });
        }

        // Fullscreen enforcement
        if (requireFullscreen) {
            document.addEventListener('fullscreenchange', function() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(() => {});
                }
            });
            document.documentElement.requestFullscreen().catch(() => {});
        }

        // Disable copy/paste
        if (disableCopyPaste) {
            document.addEventListener('copy', e => e.preventDefault());
            document.addEventListener('paste', e => e.preventDefault());
            document.addEventListener('cut', e => e.preventDefault());
        }

        // Keyboard shortcut for navigation
        document.addEventListener('keydown', function(e) {
            const current = document.querySelector('.question-card[style*="block"]');
            if (!current) return;
            const idx = parseInt(current.dataset.q);

            if (e.key === 'ArrowRight' || e.key === 'n') {
                if (idx < questions - 1) showQuestion(idx + 1);
            } else if (e.key === 'ArrowLeft' || e.key === 'p') {
                if (idx > 0) showQuestion(idx - 1);
            }
        });

        // Warn before leaving
        window.addEventListener('beforeunload', function(e) {
            e.preventDefault();
            e.returnValue = '';
        });
    </script>
</body>
</html>
