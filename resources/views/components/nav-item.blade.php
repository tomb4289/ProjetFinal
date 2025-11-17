@props(['active' => false])

<a href="{{ $url }}" class="group py-3 px-5 transition-colors duration-400 hover:bg-neutral-300 flex-1 flex justify-center active:bg-neutral-400">
    <div class="flex flex-col items-center">

        <x-dynamic-component :component="'lucide-' . $icon" class="w-12 stroke-icon transition-colors duration-400 delay-75 group-hover:stroke-icon-hover group-active:stroke-neutral-600"/>
    
        <span class="text-sm stroke-icon transition-colors duration-400 group-hover:text-icon-hover group-active:text-neutral-600 font-heading {{ $active ? 'text-primary' : '' }}">{{ $label }}</span>
    </div> 
</a>