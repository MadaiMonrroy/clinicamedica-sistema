<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <p class="text-sm text-gray-500 dark:text-gray-400">
        Mostrando {{ $staff->firstItem() ?? 0 }} a {{ $staff->lastItem() ?? 0 }} de {{ $staff->total() }} registros
    </p>

    <div class="flex items-center justify-end">
        @if ($staff->lastPage() > 1)
            <nav class="inline-flex items-center rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm">
                {{-- Anterior --}}
                @if ($staff->onFirstPage())
                    <span class="px-4 py-2 text-sm text-gray-400 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 cursor-not-allowed">
                        ‹
                    </span>
                @else
                    <a href="{{ $staff->previousPageUrl() }}"
                       class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        ‹
                    </a>
                @endif

                {{-- Números --}}
                @for ($page = 1; $page <= $staff->lastPage(); $page++)
                    @if ($page == $staff->currentPage())
                        <span class="px-4 py-2 text-sm font-semibold text-white bg-[#44B0B3] border-r border-gray-200 dark:border-gray-700">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $staff->url($page) }}"
                           class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            {{ $page }}
                        </a>
                    @endif
                @endfor

                {{-- Siguiente --}}
                @if ($staff->hasMorePages())
                    <a href="{{ $staff->nextPageUrl() }}"
                       class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        ›
                    </a>
                @else
                    <span class="px-4 py-2 text-sm text-gray-400 bg-white dark:bg-gray-800 cursor-not-allowed">
                        ›
                    </span>
                @endif
            </nav>
        @else
            <nav class="inline-flex items-center rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm">
                <span class="px-4 py-2 text-sm text-gray-400 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 cursor-not-allowed">
                    ‹
                </span>

                <span class="px-4 py-2 text-sm font-semibold text-white bg-[#44B0B3] border-r border-gray-200 dark:border-gray-700">
                    1
                </span>

                <span class="px-4 py-2 text-sm text-gray-400 bg-white dark:bg-gray-800 cursor-not-allowed">
                    ›
                </span>
            </nav>
        @endif
    </div>
</div>