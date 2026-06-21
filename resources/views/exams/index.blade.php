@section('title', 'Exams')

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Exams</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Manage examination schedules and records</p>
            </div>
            @if(Auth::user()->hasRole('Teacher'))
                <a href="{{ route('exams.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-sky-600 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-sky-700 hover:to-cyan-700 transition shadow-sm">
                    <i class="fa-solid fa-plus"></i> New Exam
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        @if(Auth::user()->hasRole('Teacher') || Auth::user()->hasRole('Admin'))
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-4">
                    <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Total Exams</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-slate-200 mt-1">{{ $exams->total() }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-4">
                    <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Published</p>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ $exams->where('is_published', true)->count() }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-4">
                    <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Online</p>
                    <p class="text-2xl font-bold text-sky-600 dark:text-sky-400 mt-1">{{ $exams->where('exam_mode', 'online')->count() }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-4">
                    <p class="text-xs font-medium text-gray-500 dark:text-slate-400">Upcoming</p>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $exams->filter(fn($e) => $e->start_date && $e->start_date >= now())->count() }}</p>
                </div>
            </div>
        @endif

        <x-data-table title="All Exams" :data="$exams" searchable="true" searchPlaceholder="Search exams..." searchValue="{{ request('search') }}" searchRoute="{{ route('exams.index') }}">
            <x-slot name="filters">
                <select name="type" onchange="const p=new URLSearchParams(location.search);p.set('type',this.value);p.delete('page');window.location='{{ route('exams.index') }}?'+p"
                    class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 pl-3 pr-8">
                    <option value="">All Types</option>
                    @foreach(['Quiz','Midterm','Final','Practical','Continuous Assessment','Mock'] as $t)
                        <option value="{{ $t }}" @selected(request('type') == $t)>{{ $t }}</option>
                    @endforeach
                </select>
                <select name="status" onchange="const p=new URLSearchParams(location.search);p.set('status',this.value);p.delete('page');window.location='{{ route('exams.index') }}?'+p"
                    class="rounded-lg border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 text-sm py-2 pl-3 pr-8">
                    <option value="">All Status</option>
                    @foreach(['draft','published','completed','archived'] as $s)
                        <option value="{{ $s }}" @selected(request('status') == $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </x-slot>

            <thead class="bg-gray-50/80 dark:bg-slate-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Title</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Subject</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Class</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Type</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                @forelse($exams as $exam)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('exams.show', $exam) }}" class="text-sm font-medium text-gray-900 dark:text-slate-200 hover:text-sky-600">{{ $exam->name }}</a>
                            @if($exam->exam_mode === 'online')
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300">Online</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $exam->subject?->name ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $exam->class?->name ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $exam->type ?? '—' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-slate-300">{{ $exam->start_date ? \Carbon\Carbon::parse($exam->start_date)->format('M d, Y') : ($exam->exam_date ? \Carbon\Carbon::parse($exam->exam_date)->format('M d, Y') : '—') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($exam->status === 'published') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300
                                @elseif($exam->status === 'draft') bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300
                                @elseif($exam->status === 'completed') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                                @elseif($exam->status === 'archived') bg-gray-100 text-gray-600 dark:bg-gray-700/50 dark:text-gray-400
                                @else bg-gray-100 text-gray-600 dark:bg-gray-700/50 dark:text-gray-400 @endif">
                                {{ ucfirst($exam->status ?? 'draft') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                            <a href="{{ route('exams.show', $exam) }}" class="inline-flex items-center justify-center w-8 h-8 text-sky-600 hover:text-white hover:bg-sky-600 rounded-lg transition" title="View">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            @if(Auth::user()->hasRole('Teacher'))
                                <a href="{{ route('exams.edit', $exam) }}" class="inline-flex items-center justify-center w-8 h-8 text-amber-600 hover:text-white hover:bg-amber-600 rounded-lg transition" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <button @click="$dispatch('set-confirmation', {
                                    action: '{{ route('exams.destroy', $exam) }}',
                                    method: 'DELETE',
                                    title: 'Delete Exam',
                                    message: 'Delete this exam? This action cannot be undone.',
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
                            <i class="fa-solid fa-pen-to-square text-3xl text-gray-200 dark:text-slate-600 mb-2"></i>
                            <p class="text-sm text-gray-400 dark:text-slate-500">No exams found.</p>
                            @if(Auth::user()->hasRole('Teacher'))
                                <a href="{{ route('exams.create') }}" class="mt-2 inline-flex items-center text-sm text-sky-600 hover:text-sky-800">Create your first exam</a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-data-table>
    </div>
</x-app-layout>
