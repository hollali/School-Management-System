@props([
    'title' => '',
    'data' => null,
    'searchable' => false,
    'searchPlaceholder' => 'Search...',
    'searchValue' => '',
    'searchRoute' => '',
    'perPage' => null,
    'perPageOptions' => [10, 25, 50, 100],
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden']) }}>

    @if ($title || $actions ?? false)
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between gap-4">
            @if ($title)
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">{{ $title }}</h3>
            @else
                <div></div>
            @endif

            @if ($actions ?? false)
                <div class="flex items-center gap-2 shrink-0">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    @if ($searchable || $filters ?? false)
        <div class="px-6 py-3 border-b border-gray-200 dark:border-slate-700 flex flex-col sm:flex-row items-start sm:items-center gap-3">
            @if ($searchable)
                <form action="{{ $searchRoute }}" method="GET" class="relative min-w-0 w-full sm:w-auto">
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-4 w-4 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            name="search"
                            value="{{ $searchValue }}"
                            placeholder="{{ $searchPlaceholder }}"
                            class="block w-full sm:w-64 rounded-lg border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-slate-200 pl-10 pr-3 py-2 text-sm placeholder-gray-400 dark:placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        />
                    </div>

                    @foreach (request()->except('search', 'page') as $key => $value)
                        @if (is_array($value))
                            @foreach ($value as $v)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}" />
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
                        @endif
                    @endforeach
                </form>
            @endif

            @if ($filters ?? false)
                <div class="flex items-center gap-2 flex-wrap">
                    {{ $filters }}
                </div>
            @endif
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
            {{ $slot }}
        </table>
    </div>

    @if ($data && $data->total() > 0)
        <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <p class="text-sm text-gray-600 dark:text-slate-400">
                    @if ($data->firstItem())
                        Showing <span class="font-semibold text-gray-900 dark:text-slate-200">{{ $data->firstItem() }}</span>
                        to <span class="font-semibold text-gray-900 dark:text-slate-200">{{ $data->lastItem() }}</span>
                    @else
                        {{ $data->count() }}
                    @endif
                    of <span class="font-semibold text-gray-900 dark:text-slate-200">{{ $data->total() }}</span> results
                </p>

                @if ($perPage || request()->has('perPage'))
                    <form action="{{ url()->current() }}" method="GET" class="flex items-center gap-1.5">
                        <label class="text-xs text-gray-500 dark:text-slate-400 font-medium">Per page</label>
                        <select name="perPage" onchange="this.form.submit()"
                            class="text-xs rounded-lg border-gray-200 dark:border-slate-600 py-1.5 pl-2 pr-6 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 bg-white dark:bg-slate-700 text-gray-900 dark:text-slate-200">
                            @foreach ($perPageOptions as $option)
                                <option value="{{ $option }}" {{ (request('perPage', $perPage ?? 10) == $option) ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                        @foreach (request()->except('perPage', 'page') as $key => $value)
                            @if (is_array($value))
                                @foreach ($value as $v)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $v }}" />
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
                            @endif
                        @endforeach
                    </form>
                @endif
            </div>

            {{ $data->links() }}
        </div>
    @endif

</div>
