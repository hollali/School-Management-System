<x-app-layout>
    @section('title', 'Assignment Feedback')

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Assignment Feedback</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Provide and review feedback on submissions</p>
        </div>
    </x-slot>

    <div class="py-6">
        <x-data-table title="Assignment Feedback" :data="$feedbacks" searchable="true" searchPlaceholder="Search feedback..." searchValue="{{ request('search') }}" searchRoute="{{ route('assignment-feedback.index') }}">
            <x-slot name="actions">
                <button @click="$dispatch('open-modal', 'create-feedback')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                    <i class="fa-solid fa-plus"></i>
                    New Feedback
                </button>
            </x-slot>
            <x-slot name="filters">
                <select name="teacher_id" @change="const p=new URLSearchParams(location.search);p.set('teacher_id',$event.target.value);p.delete('page');window.location='{{ route('assignment-feedback.index') }}?'+p"
                    class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 pl-3 pr-8 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">All Teachers</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" @selected(request('teacher_id') == $teacher->id)>{{ $teacher->user->name }}</option>
                    @endforeach
                </select>
                <select name="submission_id" @change="const p=new URLSearchParams(location.search);p.set('submission_id',$event.target.value);p.delete('page');window.location='{{ route('assignment-feedback.index') }}?'+p"
                    class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 pl-3 pr-8 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">All Submissions</option>
                    @foreach($submissions as $submission)
                        <option value="{{ $submission->id }}" @selected(request('submission_id') == $submission->id)>
                            {{ $submission->student->user->name }} — {{ $submission->assignment->title }}
                        </option>
                    @endforeach
                </select>
            </x-slot>
            <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Student</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Assignment</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Teacher</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Feedback</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Given At</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($feedbacks as $feedback)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-slate-200 font-medium">{{ $feedback->submission->student->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $feedback->submission->assignment->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $feedback->teacher?->user?->name ?? '—' }}</td>
                        <td class="px-6 py-4 max-w-xs text-sm text-gray-600 dark:text-slate-300 truncate">
                            @if($feedback->score !== null)
                                <span class="font-medium text-gray-900 dark:text-slate-200">{{ $feedback->score }}</span> —
                            @endif
                            {{ $feedback->comments ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $feedback->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                            <button @click="
                                $dispatch('view-feedback', {
                                    student: '{{ $feedback->submission->student->user->name }}',
                                    assignment: '{{ $feedback->submission->assignment->title }}',
                                    score: '{{ $feedback->score ?? '—' }}',
                                    comments: '{{ $feedback->comments ?? '—' }}',
                                    teacher: '{{ $feedback->teacher?->user?->name ?? '—' }}',
                                    date: '{{ $feedback->created_at->format('M d, Y') }}'
                                });
                                $dispatch('open-modal', 'view-feedback');
                            " class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition" title="View">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            <button @click="
                                $dispatch('edit-feedback', {
                                    id: '{{ $feedback->id }}',
                                    submission_id: '{{ $feedback->submission_id }}',
                                    teacher_id: '{{ $feedback->teacher_id }}',
                                    score: '{{ $feedback->score }}',
                                    comments: '{{ $feedback->comments }}'
                                });
                                $dispatch('open-modal', 'edit-feedback');
                            " class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button @click="$dispatch('set-confirmation', {
                                action: '{{ route('assignment-feedback.destroy', $feedback) }}',
                                method: 'DELETE',
                                title: 'Delete Feedback',
                                message: 'Delete this feedback? This action cannot be undone.',
                                confirmLabel: 'Delete',
                                confirmClass: 'bg-red-600 hover:bg-red-700'
                            })" class="inline-flex items-center justify-center w-8 h-8 text-red-500 hover:text-white hover:bg-red-500 rounded-lg transition" title="Delete">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-gray-400 dark:text-slate-500">No feedback found.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>

    <x-modal name="create-feedback" maxWidth="2xl" focusable>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ __('New Feedback') }}</h2>
                <button @click="$dispatch('close-modal', 'create-feedback')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-white transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('assignment-feedback.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="submission_id" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Submission</label>
                        <select name="submission_id" id="submission_id"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                            <option value="">Select Submission</option>
                            @foreach($submissions as $submission)
                                <option value="{{ $submission->id }}" {{ old('submission_id') == $submission->id ? 'selected' : '' }}>
                                    {{ $submission->student->user->name }} — {{ $submission->assignment->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('submission_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="teacher_id" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Teacher</label>
                        <select name="teacher_id" id="teacher_id"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                            <option value="">Select Teacher</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->user->name }}</option>
                            @endforeach
                        </select>
                        @error('teacher_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="score" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Score</label>
                        <input type="number" name="score" id="score" value="{{ old('score') }}" step="0.01" min="0" max="100"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                        @error('score')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="comments" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Comments</label>
                        <textarea name="comments" id="comments" rows="4"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">{{ old('comments') }}</textarea>
                        @error('comments')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <button @click="$dispatch('close-modal', 'create-feedback')" type="button"
                            class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                            {{ __('Create Feedback') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="edit-feedback" maxWidth="2xl" focusable>
        <div class="p-6" x-data="editFeedbackData()" @edit-feedback.window="load($event.detail)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Edit Feedback</h2>
                <button @click="$dispatch('close-modal', 'edit-feedback')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-white transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form method="POST" :action="`/assignment-feedback/${form.id}`">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="edit_submission_id" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Submission</label>
                        <select name="submission_id" id="edit_submission_id" x-model="form.submission_id"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                            <option value="">Select Submission</option>
                            @foreach($submissions as $submission)
                                <option value="{{ $submission->id }}">
                                    {{ $submission->student->user->name }} — {{ $submission->assignment->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="edit_teacher_id" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Teacher</label>
                        <select name="teacher_id" id="edit_teacher_id" x-model="form.teacher_id"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                            <option value="">Select Teacher</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="edit_score" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Score</label>
                        <input type="number" name="score" id="edit_score" step="0.01" min="0" max="100" x-model="form.score"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                    </div>
                    <div>
                        <label for="edit_comments" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Comments</label>
                        <textarea name="comments" id="edit_comments" rows="4" x-model="form.comments"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-4">
                        <button @click="$dispatch('close-modal', 'edit-feedback')" type="button"
                            class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                            {{ __('Update Feedback') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="view-feedback" maxWidth="xl" focusable>
        <div class="p-6" x-data="{ feedback: null }" @view-feedback.window="feedback = $event.detail">
            <template x-if="feedback">
                <div>
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Feedback Details</h2>
                        <button @click="$dispatch('close-modal', 'view-feedback')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-white">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                    <dl class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500 dark:text-slate-400 font-medium">Student</dt>
                            <dd class="text-gray-900 dark:text-slate-200 mt-1" x-text="feedback.student"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-slate-400 font-medium">Assignment</dt>
                            <dd class="text-gray-900 dark:text-slate-200 mt-1" x-text="feedback.assignment"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-slate-400 font-medium">Score</dt>
                            <dd class="text-gray-900 dark:text-slate-200 mt-1" x-text="feedback.score"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-slate-400 font-medium">Teacher</dt>
                            <dd class="text-gray-900 dark:text-slate-200 mt-1" x-text="feedback.teacher"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-slate-400 font-medium">Date</dt>
                            <dd class="text-gray-900 dark:text-slate-200 mt-1" x-text="feedback.date"></dd>
                        </div>
                        <div class="col-span-2">
                            <dt class="text-gray-500 dark:text-slate-400 font-medium">Comments</dt>
                            <dd class="text-gray-900 dark:text-slate-200 mt-1 whitespace-pre-wrap" x-text="feedback.comments"></dd>
                        </div>
                    </dl>
                </div>
            </template>
        </div>
    </x-modal>

    <script>
    function editFeedbackData() {
        return {
            form: { id: '', submission_id: '', teacher_id: '', score: '', comments: '' },
            load(data) {
                this.form = { ...data };
            }
        };
    }
    </script>
</x-app-layout>
