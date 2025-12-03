@props([
    'title' => 'Rien ici pour le moment',
    'subtitle' => null,
    'actionLabel' => null,
    'actionUrl' => null,
])

<div 
    class="w-full flex justify-center"
    role="status"
    aria-live="polite"
>
    <div class="w-full max-w-md rounded-2xl border border-border-base bg-white/80 backdrop-blur-sm px-6 py-8 
                shadow-sm flex flex-col items-center text-center gap-4
                sm:px-8 sm:py-10">

        {{-- Titre --}}
        <div class="space-y-1">
            <h2 class="text-base sm:text-lg font-semibold text-neutral-800">
                {{ $title }}
            </h2>

            @if ($subtitle)
                <p class="text-sm text-neutral-800">
                    {{ $subtitle }}
                </p>
            @endif
        </div>

        {{-- Bouton d’action (optionnel) --}}
        @if($actionLabel && $actionUrl)
            <a href="{{ $actionUrl }}"
               class="group inline-flex items-center gap-2 bg-button-default border-2 border-primary font-bold py-2 px-4 rounded-lg
                      hover:bg-button-hover active:bg-primary-active transition-colors duration-300 text-center">

                <span class="text-primary group-hover:text-white transition-colors">
                    {{ $actionLabel }}
                </span>

                <svg class="h-4 w-4 text-primary group-hover:text-white transition-colors"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 12h14"></path>
                    <path d="M12 5l7 7-7 7"></path>
                </svg>
            </a>
        @endif
    </div>
</div>
