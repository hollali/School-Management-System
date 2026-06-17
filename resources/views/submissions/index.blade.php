<x-app-layout>
    @section('title', 'Submissions')

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Submissions</h2>
            <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Review and grade student submissions</p>
        </div>
    </x-slot>

    <div class="py-6">
        <x-data-table title="Submissions" :data="$submissions" searchable="true" searchPlaceholder="Search submissions..." searchValue="{{ request('search') }}" searchRoute="{{ route('submissions.index') }}">
            <x-slot name="filters">
                <select name="assignment_id" @change="const p=new URLSearchParams(location.search);p.set('assignment_id',$event.target.value);p.delete('page');window.location='{{ route('submissions.index') }}?'+p"
                    class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 pl-3 pr-8 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">All Assignments</option>
                    @foreach($assignments as $assignment)
                        <option value="{{ $assignment->id }}" @selected(request('assignment_id') == $assignment->id)>{{ $assignment->title }}</option>
                    @endforeach
                </select>
                <select name="status" @change="const p=new URLSearchParams(location.search);p.set('status',$event.target.value);p.delete('page');window.location='{{ route('submissions.index') }}?'+p"
                    class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 pl-3 pr-8 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    <option value="submitted" @selected(request('status') === 'submitted')>Submitted</option>
                    <option value="graded" @selected(request('status') === 'graded')>Graded</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                </select>
            </x-slot>
            <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Student</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Assignment</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Submitted At</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Grade</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($submissions as $submission)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-slate-200 font-medium">{{ $submission->student->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $submission->assignment->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $submission->submitted_at?->format('M d, Y H:i') ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $subStatusColors = [
                                    'submitted' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-200',
                                    'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-200',
                                    'graded' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200',
                                ];
                                $subColor = $subStatusColors[$submission->status] ?? 'bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-slate-200';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $subColor }}">
                                {{ ucfirst($submission->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $submission->grade ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                            <button @click="
                                $dispatch('view-submission', @js([
                                    'assignment' => $submission->assignment->title,
                                    'student' => $submission->student->user->name,
                                    'submitted_at' => $submission->submitted_at?->format('M d, Y H:i') ?? '—',
                                    'status' => ucfirst($submission->status),
                                    'statusColor' => $subStatusColors[$submission->status] ?? 'bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-slate-200',
                                    'content' => $submission->content ?? '—',
                                    'has_attachment' => $submission->attachment_path ? true : false,
                                    'attachment_url' => $submission->attachment_path ? Storage::url($submission->attachment_path) : '',
                                ]));
                                $dispatch('open-modal', 'view-submission');
                            " class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition" title="View">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            @if(Auth::user()->hasRole('Teacher'))
                                <button @click="
                                    $dispatch('edit-submission', @js([
                                        'id' => $submission->id,
                                        'assignment_id' => $submission->assignment_id,
                                        'student_id' => $submission->student_id,
                                        'content' => $submission->content ?? '',
                                    ]));
                                    $dispatch('open-modal', 'edit-submission');
                                " class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button @click="$dispatch('set-confirmation', {
                                    action: '{{ route('submissions.destroy', $submission) }}',
                                    method: 'DELETE',
                                    title: 'Delete Submission',
                                    message: 'Delete this submission? This action cannot be undone.',
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
                        <td colspan="6" class="text-center py-12 text-gray-400 dark:text-slate-500">No submissions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>

    <x-modal name="create-submission" maxWidth="2xl" focusable>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ __('New Submission') }}</h2>
                <button @click="$dispatch('close-modal', 'create-submission')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-white transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form action="{{ route('submissions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 gap-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Assignment</label>
                            <select name="assignment_id"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                                <option value="">Select Assignment</option>
                                @foreach($assignments as $assignment)
                                    <option value="{{ $assignment->id }}" {{ old('assignment_id') == $assignment->id ? 'selected' : '' }}>{{ $assignment->title }}</option>
                                @endforeach
                            </select>
                            @error('assignment_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Student</label>
                            <select name="student_id"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                                <option value="">Select Student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>{{ $student->user->name }}</option>
                                @endforeach
                            </select>
                            @error('student_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Content</label>
                        <textarea name="content" rows="5"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">{{ old('content') }}</textarea>
                        @error('content')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Attachment</label>
                        <div class="mt-1 flex items-center gap-3">
                            <label class="cursor-pointer inline-flex items-center px-4 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                Choose file
                                <input type="file" name="attachment" class="hidden">
                            </label>
                            <span class="text-xs text-gray-400 dark:text-slate-500">Max 10 MB</span>
                        </div>
                        @error('attachment')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex items-center justify-end gap-3">
                        <button @click="$dispatch('close-modal', 'create-submission')" type="button"
                            class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                            Create submission
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="edit-submission" maxWidth="2xl" focusable>
        <div class="p-6" x-data="editSubmissionData()" @edit-submission.window="load($event.detail)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">{{ __('Edit Submission') }}</h2>
                <button @click="$dispatch('close-modal', 'edit-submission')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-white transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <form method="POST" :action="`/submissions/${form.id}`" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 gap-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Assignment</label>
                            <select name="assignment_id" x-model="form.assignment_id"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                                <option value="">Select Assignment</option>
                                @foreach($assignments as $assignment)
                                    <option value="{{ $assignment->id }}">{{ $assignment->title }}</option>
                                @endforeach
                            </select>
                            @error('assignment_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Student</label>
                            <select name="student_id" x-model="form.student_id"
                                class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200">
                                <option value="">Select Student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->user->name }}</option>
                                @endforeach
                            </select>
                            @error('student_id')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Content</label>
                        <textarea name="content" rows="5" x-model="form.content"
                            class="block w-full rounded-xl border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 dark:bg-slate-700 dark:text-slate-200"></textarea>
                        @error('content')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Attachment (replace if new)</label>
                        <div class="mt-1 flex items-center gap-3">
                            <label class="cursor-pointer inline-flex items-center px-4 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                Choose file
                                <input type="file" name="attachment" class="hidden">
                            </label>
                            <span class="text-xs text-gray-400 dark:text-slate-500">Max 10 MB</span>
                        </div>
                        @error('attachment')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex items-center justify-end gap-3">
                        <button @click="$dispatch('close-modal', 'edit-submission')" type="button"
                            class="inline-flex items-center px-6 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                            Update submission
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>

    <x-modal name="view-submission" maxWidth="2xl" focusable>
        <div class="p-6" x-data="viewSubmissionData()" @view-submission.window="load($event.detail)">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-200">Submission Details</h2>
                <button @click="$dispatch('close-modal', 'view-submission')" type="button" class="text-gray-400 dark:text-slate-500 hover:text-gray-600 dark:hover:text-white">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-4">
                    <p class="text-sm text-gray-500 dark:text-slate-400">Assignment</p>
                    <p class="text-sm text-gray-900 dark:text-slate-200 font-medium mt-1" x-text="data.assignment"></p>
                </div>
                <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-4">
                    <p class="text-sm text-gray-500 dark:text-slate-400">Student</p>
                    <p class="text-sm text-gray-900 dark:text-slate-200 font-medium mt-1" x-text="data.student"></p>
                </div>
                <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-4">
                    <p class="text-sm text-gray-500 dark:text-slate-400">Submitted At</p>
                    <p class="text-sm text-gray-900 dark:text-slate-200 font-medium mt-1" x-text="data.submitted_at"></p>
                </div>
                <div class="bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-4">
                    <p class="text-sm text-gray-500 dark:text-slate-400">Status</p>
                    <p class="mt-1">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" :class="data.statusColor" x-text="data.status"></span>
                    </p>
                </div>
            </div>
            <div class="mt-4 bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-4" x-show="data.content && data.content !== '—'">
                <p class="text-sm text-gray-500 dark:text-slate-400">Content</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-slate-200 whitespace-pre-wrap" x-text="data.content"></p>
            </div>
            <div class="mt-4 bg-gray-50/50 dark:bg-slate-700/30 rounded-xl p-4" x-show="data.has_attachment">
                <p class="text-sm text-gray-500 dark:text-slate-400">Attachment</p>
                <div class="mt-2">
                    <a :href="data.attachment_url" target="_blank"
                        class="inline-flex items-center px-3 py-1.5 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-200 text-xs font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600 transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Download attachment
                    </a>
                </div>
            </div>
        </div>
    </x-modal>

    <script>
    function editSubmissionData() {
        return {
            form: { id: '', assignment_id: '', student_id: '', content: '' },
            load(data) {
                this.form = { ...data };
            }
        };
    }
    function viewSubmissionData() {
        return {
            data: { assignment: '', student: '', submitted_at: '', status: '', statusColor: '', content: '', has_attachment: false, attachment_url: '' },
            load(data) {
                this.data = { ...data };
            }
        };
    }
    </script>
</x-app-layout>
