{{-- Pagination alignée sur la charte FOLEM, surcharge de la vue par défaut. --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between gap-4">

        {{-- Mobile --}}
        <div class="flex flex-1 justify-between gap-3 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="cursor-default rounded-lg border border-gray-200 px-4 py-2 text-theme-sm text-gray-400 dark:border-gray-800">
                    Précédent
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="rounded-lg border border-gray-300 px-4 py-2 text-theme-sm text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                    Précédent
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="rounded-lg border border-gray-300 px-4 py-2 text-theme-sm text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
                    Suivant
                </a>
            @else
                <span class="cursor-default rounded-lg border border-gray-200 px-4 py-2 text-theme-sm text-gray-400 dark:border-gray-800">
                    Suivant
                </span>
            @endif
        </div>

        {{-- Bureau --}}
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <p class="text-theme-xs text-gray-500 dark:text-gray-400">
                Résultats {{ $paginator->firstItem() }} à {{ $paginator->lastItem() }}
                sur {{ $paginator->total() }}
            </p>

            <div class="flex items-center gap-1">
                @if ($paginator->onFirstPage())
                    <span class="flex size-9 cursor-default items-center justify-center rounded-lg text-gray-300" aria-disabled="true">&laquo;</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Page précédente"
                       class="flex size-9 items-center justify-center rounded-lg text-gray-600 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5">&laquo;</a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="flex size-9 cursor-default items-center justify-center text-gray-400">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page"
                                      class="flex size-9 items-center justify-center rounded-lg bg-brand-600 text-theme-sm font-semibold text-white">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}"
                                   class="flex size-9 items-center justify-center rounded-lg text-theme-sm text-gray-600 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Page suivante"
                       class="flex size-9 items-center justify-center rounded-lg text-gray-600 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5">&raquo;</a>
                @else
                    <span class="flex size-9 cursor-default items-center justify-center rounded-lg text-gray-300" aria-disabled="true">&raquo;</span>
                @endif
            </div>
        </div>
    </nav>
@endif
