<x-app-layout>
    @section('title', $announcement->title)

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">{{ $announcement->title }}</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Announcement details</p>
            </div>
            <div class="flex items-center gap-2">
                @can('update', $announcement)
                    <a href="{{ route('announcements.edit', $announcement) }}"
                        class="inline-flex items-center justify-center w-9 h-9 text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:text-slate-500 dark:hover:text-slate-400 dark:hover:bg-slate-600 rounded-xl transition">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                @endcan
                @can('delete', $announcement)
                    <form action="{{ route('announcements.destroy', $announcement) }}" method="POST" onsubmit="return confirm('Delete this announcement?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center justify-center w-9 h-9 text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>
                @endcan
                <a href="{{ route('announcements.index') }}"
                    class="inline-flex items-center justify-center w-9 h-9 text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:text-slate-500 dark:hover:text-slate-400 dark:hover:bg-slate-600 rounded-xl transition">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-8">
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    @if($announcement->target_student_id)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-300">
                            Student: {{ $announcement->targetStudent?->user?->name ?? '—' }}
                        </span>
                    @elseif($announcement->target_role)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300">
                            {{ ucfirst($announcement->target_role) }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-slate-700 dark:text-slate-300">
                            All Users
                        </span>
                    @endif
                    @if($announcement->targetClass)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">
                            {{ $announcement->targetClass->name }}
                        </span>
                    @endif
                </div>

                <p class="text-xs text-gray-400 dark:text-slate-500 mb-2">
                    Published {{ $announcement->published_at?->format('F d, Y \a\t h:i A') ?? '—' }}
                    by {{ $announcement->publisher?->name ?? '—' }}
                </p>

                <div class="prose prose-sm max-w-none text-gray-600 dark:text-slate-400 mt-4">
                    {!! nl2br(e($announcement->body)) !!}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
