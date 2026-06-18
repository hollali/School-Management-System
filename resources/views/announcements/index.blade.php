<x-app-layout>
    @section('title', __('Announcements'))

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">{{ __('Announcements') }}</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">System announcements and notices</p>
            </div>
            @can('create', App\Models\Announcement::class)
                <a href="{{ route('announcements.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-xl transition">
                    <i class="fa-solid fa-plus mr-2"></i>
                    {{ __('New Announcement') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
                @forelse($announcements as $announcement)
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700/50 transition group">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('announcements.show', $announcement) }}" class="hover:no-underline">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-slate-200 group-hover:text-sky-600 dark:group-hover:text-sky-400 transition">{{ $announcement->title }}</p>
                                        @if($announcement->target_student_id)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-300">
                                                Student: {{ $announcement->targetStudent?->user?->name ?? '—' }}
                                            </span>
                                        @elseif($announcement->target_role)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300">
                                                {{ ucfirst($announcement->target_role) }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-slate-300">
                                                All
                                            </span>
                                        @endif
                                        @if($announcement->targetClass)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">
                                                {{ $announcement->targetClass->name }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-1 line-clamp-2">
                                        {{ $announcement->body }}
                                    </p>
                                </a>
                            </div>
                            <div class="flex items-start gap-3 shrink-0">
                                <div class="text-xs text-gray-400 dark:text-slate-500 whitespace-nowrap text-right pt-0.5 hidden sm:block">
                                    <div>{{ $announcement->published_at?->diffForHumans() }}</div>
                                    <div class="mt-1">{{ $announcement->publisher?->name }}</div>
                                </div>
                                <div class="flex items-center gap-1 pt-0.5">
                                    <a href="{{ route('announcements.show', $announcement) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-sky-600 hover:bg-sky-50 dark:hover:text-sky-400 dark:hover:bg-sky-900/20 rounded-lg transition"
                                        title="View">
                                        <i class="fa-solid fa-eye text-sm"></i>
                                    </a>
                                    @can('update', $announcement)
                                        <a href="{{ route('announcements.edit', $announcement) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:text-amber-400 dark:hover:bg-amber-900/20 rounded-lg transition"
                                            title="Edit">
                                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                                        </a>
                                    @endcan
                                    @can('delete', $announcement)
                                        <button type="button"
                                            @click="$dispatch('set-confirmation', {
                                                action: '{{ route('announcements.destroy', $announcement) }}',
                                                method: 'DELETE',
                                                title: 'Delete Announcement',
                                                message: 'Are you sure you want to delete \"{{ $announcement->title }}\"? This action cannot be undone.',
                                                confirmLabel: 'Delete',
                                                confirmClass: 'bg-red-600 hover:bg-red-700'
                                            })"
                                            class="inline-flex items-center justify-center w-8 h-8 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:text-red-400 dark:hover:bg-red-900/20 rounded-lg transition"
                                            title="Delete">
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <i class="fa-solid fa-bullhorn text-4xl text-gray-200 dark:text-slate-600 mb-4"></i>
                        <p class="text-sm text-gray-500 dark:text-slate-400">No announcements yet.</p>
                    </div>
                @endforelse

                <div class="p-4 border-t border-gray-100 dark:border-slate-700">
                    {{ $announcements->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
