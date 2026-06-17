@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">

        {{-- Mobile view --}}
        <div class="flex gap-2 items-center justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-gray-400 dark:text-slate-500 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 cursor-not-allowed leading-5 rounded-xl shadow-sm">
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 leading-5 rounded-xl shadow-sm hover:bg-gray-50 dark:hover:bg-slate-600 hover:border-gray-300 dark:hover:border-slate-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-400 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-slate-300 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 leading-5 rounded-xl shadow-sm hover:bg-gray-50 dark:hover:bg-slate-600 hover:border-gray-300 dark:hover:border-slate-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-400 transition ease-in-out duration-150">
                    {!! __('pagination.next') !!}
                    <svg class="w-4 h-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <span class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-gray-400 dark:text-slate-500 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 cursor-not-allowed leading-5 rounded-xl shadow-sm">
                    {!! __('pagination.next') !!}
                    <svg class="w-4 h-4 ml-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </span>
            @endif
        </div>

        {{-- Desktop view --}}
        <div class="hidden sm:flex-1 sm:flex sm:gap-4 sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-slate-400 leading-5">
                    {!! __('Showing') !!}
                    @if ($paginator->firstItem())
                        <span class="font-semibold text-gray-900 dark:text-slate-200">{{ $paginator->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-semibold text-gray-900 dark:text-slate-200">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    {!! __('of') !!}
                    <span class="font-semibold text-gray-900 dark:text-slate-200">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>

            <div>
                <span class="inline-flex gap-1 rtl:flex-row-reverse">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span class="inline-flex items-center justify-center w-9 h-9 text-sm font-semibold text-gray-300 dark:text-slate-500 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 cursor-not-allowed rounded-xl shadow-sm" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-9 h-9 text-sm font-semibold text-gray-600 dark:text-slate-300 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl shadow-sm hover:bg-gray-50 dark:hover:bg-slate-600 hover:border-gray-300 dark:hover:border-slate-500 hover:text-gray-800 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-400 transition ease-in-out duration-150" aria-label="{{ __('pagination.previous') }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="inline-flex items-center justify-center w-9 h-9 text-sm font-semibold text-gray-400 dark:text-slate-400 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 cursor-default rounded-xl shadow-sm">{{ $element }}</span>
                            </span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="inline-flex items-center justify-center w-9 h-9 text-sm font-bold text-white bg-gradient-to-r from-sky-600 to-cyan-600 border border-transparent rounded-xl shadow-sm">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex items-center justify-center w-9 h-9 text-sm font-semibold text-gray-600 dark:text-slate-300 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl shadow-sm hover:bg-gray-50 dark:hover:bg-slate-600 hover:border-gray-300 dark:hover:border-slate-500 hover:text-gray-800 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-400 transition ease-in-out duration-150" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-9 h-9 text-sm font-semibold text-gray-600 dark:text-slate-300 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl shadow-sm hover:bg-gray-50 dark:hover:bg-slate-600 hover:border-gray-300 dark:hover:border-slate-500 hover:text-gray-800 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-400 transition ease-in-out duration-150" aria-label="{{ __('pagination.next') }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span class="inline-flex items-center justify-center w-9 h-9 text-sm font-semibold text-gray-300 dark:text-slate-500 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 cursor-not-allowed rounded-xl shadow-sm" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
