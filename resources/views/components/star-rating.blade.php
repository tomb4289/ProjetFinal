@props([
    'rating' => null,
    'maxRating' => 5,
    'editable' => false,
    'name' => 'rating',
    'id' => null
])

@php
    $currentRating = $rating ?? 0;
    $uniqueId = $id ?? 'rating-' . uniqid();
@endphp

<div 
    class="star-rating-container w-full" 
    data-rating="{{ $currentRating }}" 
    data-max-rating="{{ $maxRating }}" 
    data-editable="{{ $editable ? 'true' : 'false' }}" 
    data-rating-id="{{ $uniqueId }}"
    role="{{ $editable ? 'group' : 'img' }}"
    aria-label="{{ $editable ? 'Attribution d\'une note' : 'Note de ' . $currentRating . ' sur ' . $maxRating }}"
>
    <div class="flex items-center gap-1 w-full">
        @for($i = 1; $i <= $maxRating; $i++)
            @if($editable)
                <button
                    type="button"
                    class="star-btn cursor-pointer transition-colors duration-200 flex-1 {{ $i <= $currentRating ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400"
                    data-star-value="{{ $i }}"
                    aria-label="Donner la note de {{ $i }} sur {{ $maxRating }}"
                    aria-pressed="{{ $i <= $currentRating ? 'true' : 'false' }}"
                >
                    <svg class="w-full h-12" fill="currentColor" viewBox="0 0 20 20" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </button>
            @else
                {{-- En lecture seule, on masque les étoiles individuelles car le conteneur a déjà le label global --}}
                <span 
                    class="star-display transition-colors duration-200 flex-1 {{ $i <= $currentRating ? 'text-yellow-400' : 'text-gray-300' }}"
                    aria-hidden="true"
                >
                    <svg class="w-full h-12" fill="currentColor" viewBox="0 0 20 20" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </span>
            @endif
        @endfor
        @if($editable)
            <input type="hidden" name="{{ $name }}" value="{{ $currentRating }}" id="{{ $uniqueId }}-input">
            <span class="ml-2 text-sm text-text-muted whitespace-nowrap" aria-live="polite">{{ $currentRating > 0 ? $currentRating . '/5' : 'Non noté' }}</span>
        @endif
    </div>
</div>