@props([
    'href' => '#',
    'icon' => 'circle',
    'title' => '',
    'subtitle' => '',
])

<a href="{{ $href }}" class="h-24 flex items-center gap-4 p-4 rounded-lg bg-card hover:bg-card-hover shadow-sm border border-border-base transition">
    
    {{-- Icône --}}
    <div class="bg-neutral-800 text-white w-12 h-12 flex items-center justify-center rounded-full">
        <x-dynamic-component :component="'lucide-' . $icon" class="w-6 h-6 aspect-square" />
    </div>

    {{-- Textes --}}
    <div class="flex flex-col">
        <span class="font-semibold text-text-title text-lg">{{ $title }}</span>
        <span class="text-sm text-text-muted">{{ $subtitle }}</span>
    </div>

</a>
