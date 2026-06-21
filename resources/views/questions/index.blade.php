@section('title', 'Question Bank')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Question Bank</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Create and manage reusable exam questions</p>
            </div>
            @if(Auth::user()->hasRole('Teacher'))
                <a href="{{ route('questions.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                    <i class="fa-solid fa-plus"></i> New Question
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Total Questions</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-slate-200 mt-1">{{ $questions->total() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Active</p>
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $questions->where('is_active', true)->count() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400">MCQ</p>
                <p class="text-2xl font-bold text-sky-600 dark:text-sky-400 mt-1">{{ $questions->where('question_type', 'mcq')->count() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Essay</p>
                <p class="text-2xl font-bold text-violet-600 dark:text-violet-400 mt-1">{{ $questions->where('question_type', 'essay')->count() }}</p>
            </div>
        </div>

        <x-data-table title="Questions" :data="$questions" searchable="true" searchPlaceholder="Search questions..." searchValue="{{ request('search') }}" searchRoute="{{ route('questions.index') }}">
            <x-slot name="filters">
                <select name="subject_id" onchange="const p=new URLSearchParams(location.search);p.set('subject_id',this.value);p.delete('page');window.location='{{ route('questions.index') }}?'+p"
                    class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 pl-3 pr-8">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected(request('subject_id') == $subject->id)>{{ $subject->name }}</option>
                    @endforeach
                </select>
                <select name="question_type" onchange="const p=new URLSearchParams(location.search);p.set('question_type',this.value);p.delete('page');window.location='{{ route('questions.index') }}?'+p"
                    class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 pl-3 pr-8">
                    <option value="">All Types</option>
                    @foreach(['mcq','true_false','fill_blank','short_answer','essay','matching','multi_select','numeric'] as $qt)
                        <option value="{{ $qt }}" @selected(request('question_type') == $qt)>{{ ucfirst(str_replace('_', ' ', $qt)) }}</option>
                    @endforeach
                </select>
            </x-slot>

            <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Question</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Subject</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Type</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Difficulty</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Marks</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($questions as $question)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-gray-900 dark:text-slate-200 truncate max-w-xs">{{ $question->question_text }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $question->subject?->name ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300">
                                {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ ucfirst($question->difficulty) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $question->default_marks }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($question->is_active) bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300
                                @else bg-gray-100 text-gray-600 dark:bg-gray-700/50 dark:text-gray-400 @endif">
                                {{ $question->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                            <a href="{{ route('questions.show', $question) }}" class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition" title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @if(Auth::user()->hasRole('Teacher'))
                                <a href="{{ route('questions.edit', $question) }}" class="inline-flex items-center justify-center w-8 h-8 text-amber-600 hover:text-white hover:bg-amber-600 rounded-lg transition" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <button @click="$dispatch('set-confirmation', {
                                    action: '{{ route('questions.destroy', $question) }}',
                                    method: 'DELETE',
                                    title: 'Delete Question',
                                    message: 'Delete this question? This action cannot be undone.',
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
                        <td colspan="7" class="text-center py-12">
                            <i class="fa-solid fa-database text-3xl text-gray-200 dark:text-slate-600 mb-2"></i>
                            <p class="text-sm text-gray-400 dark:text-slate-500">No questions yet.</p>
                            @if(Auth::user()->hasRole('Teacher'))
                                <a href="{{ route('questions.create') }}" class="mt-2 inline-flex items-center text-sm text-sky-600 hover:text-sky-800">Create your first question</a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>
</x-app-layout>
