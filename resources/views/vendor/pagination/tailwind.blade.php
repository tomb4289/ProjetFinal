@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
        
        {{-- BLOC MOBILE / TABLETTE (< 750px) --}}
        {{-- Changement ici : 'flex-col' pour empiler les boutons et le texte --}}
        <div class="flex flex-col flex-1 min-[750px]:hidden">

            {{-- 1. LIGNE DES BOUTONS --}}
            <div class="flex justify-between w-full">
                @if ($paginator->onFirstPage())
                    <span class="px-4 py-2 text-sm bg-white text-gray-400 border border-gray-300 rounded cursor-default">
                        {!! __('pagination.previous') !!}
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}"
                       class="px-4 py-2 text-sm bg-white text-gray-800 border border-gray-300 rounded hover:bg-gray-100 transition">
                        {!! __('pagination.previous') !!}
                    </a>
                @endif

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}"
                       class="px-4 py-2 text-sm bg-white text-gray-800 border border-gray-300 rounded hover:bg-gray-100 transition ml-3">
                        {!! __('pagination.next') !!}
                    </a>
                @else
                    <span class="px-4 py-2 text-sm bg-white text-gray-400 border border-gray-300 rounded cursor-default ml-3">
                        {!! __('pagination.next') !!}
                    </span>
                @endif
            </div>

            {{-- 2. LIGNE DU TEXTE (En dessous) --}}
            <div class="mt-3 text-center">
                <p class="text-sm text-gray-500">
                <span class="font-medium text-gray-900">{{ $paginator->firstItem() }}</span>
                <span class="text-gray-400 mx-1"> à</span>
                <span class="font-medium text-gray-900">{{ $paginator->lastItem() }}</span>
                <span class="text-gray-500 ml-1">sur</span>
                <span class="font-medium text-gray-900 ml-1">{{ $paginator->total() }}</span>
                </p>
            </div>

        </div>


        {{-- BLOC DESKTOP (>= 750px) --}}
        {{-- (Ce code reste inchangé par rapport à la version précédente) --}}
        <div class="hidden min-[750px]:flex min-[750px]:flex-1 min-[750px]:items-center min-[750px]:justify-between min-[750px]:flex-col-reverse">
            <div>
                <p class="text-sm text-gray-600">
                    {!! __('Showing') !!}
                    @if ($paginator->firstItem())
                        <span class="font-medium text-gray-900">{{ $paginator->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium text-gray-900">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    {!! __('of') !!}
                    <span class="font-medium text-gray-900">{{ $paginator->total() }}</span>
                    {!! __('results') !!}
                </p>
            </div>

            <div>
                <span class="inline-flex rounded-md">
                    @if ($paginator->onFirstPage())
                        <span class="px-3 py-2 bg-white text-gray-400 border border-gray-300 rounded-l-md cursor-default">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 bg-white text-gray-800 border border-gray-300 rounded-l-md hover:bg-gray-100 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/>
                            </svg>
                        </a>
                    @endif

                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span class="px-4 py-2 bg-white text-gray-400 border border-gray-300">{{ $element }}</span>
                        @endif
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span class="px-4 py-2 bg-black text-white border border-black font-semibold">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="px-4 py-2 bg-white text-gray-800 border border-gray-300 hover:bg-gray-100 transition">{{ $page }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 bg-white text-gray-800 border border-gray-300 rounded-r-md hover:bg-gray-100 transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/>
                            </svg>
                        </a>
                    @else
                        <span class="px-3 py-2 bg-white text-gray-400 border border-gray-300 rounded-r-md cursor-default">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"/>
                            </svg>
                        </span>
                    @endif
                </span>
            </div>
        </div>

    </nav>
@endif