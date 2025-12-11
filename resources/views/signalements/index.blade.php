@extends('layouts.app')

@section('content')

<div class="mx-auto px-4 py-8 max-w-7xl">
    
    <x-page-header 
        :title="'Signalements'"
        :undertitle="'Vous avez ' . $nonLus . ' nouveau(x) signalement(s)'"
    />

    <x-back-btn route="{{ route('admin.users.index') }}" class="mt-4"/>

    <div class="mt-8">
        
       
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            
            @foreach ($signalements as $s)
                <div class="bg-card rounded-r-xl shadow-sm border border-muted/20 flex flex-col h-full hover:shadow-md transition-shadow duration-300 relative overflow-hidden group">
                    
                   
                    <div class="absolute left-0 top-0 bottom-0 w-1 {{ $s->is_read ? 'bg-green-400' : 'bg-red-400' }}"></div>
                <a href="{{ route('admin.signalements.show', $s) }}" class="flex flex-col h-full group-hover:bg-gray-50 transition-colors">
                    <div class="p-5 pb-0 flex justify-between items-start">
                        <div class="flex flex-col">
                            <span class="text-xs font-semibold text-text-muted uppercase tracking-wider mb-1">
                                {{ $s->created_at->translatedFormat('d M Y') }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200 max-w-50 truncate">
                                {{ $s->nom }}
                            </span>
                        </div>

                        @if (!$s->is_read)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-red-50 text-red-600 border border-red-100 tracking-wide">
                                Nouveau
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-green-50 text-green-600 border border-green-100 tracking-wide">
                                Traité
                            </span>
                        @endif
                    </div>

                    
                    <div class="p-5 flex-1">

                            <h3 class="font-bold text-heading text-lg leading-tight mb-2 line-clamp-1" title="{{ $s->bouteilleCatalogue->nom }}">
                                {{ $s->bouteilleCatalogue->nom }}
                            </h3>
                        
                        <p class="text-sm text-text-muted/80 leading-relaxed line-clamp-3 break-words">
                            {{ $s->description }}
                        </p>
                    </div>
                </a>

                   
                    <div class="p-4 border-t border-muted/10 bg-gray-50/50 mt-auto">
                        
                        @if (!$s->is_read)
                            
                            <form action="{{ route('admin.signalements.read', $s) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <button class="w-full inline-flex items-center justify-center gap-2 py-2 px-4 bg-white hover:bg-primary hover:text-white text-primary border-2 hover:border-primary text-sm font-semibold rounded-lg transition-all duration-200 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 6 9 17l-5-5"/>
                                    </svg>
                                    Marquer comme lu
                                </button>
                            </form>
                        @else
                            
                            <form action="{{ route('admin.signalements.destroy', $s) }}" method="POST" class="w-full" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce signalement ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 py-2 px-4 bg-white hover:bg-red-500 hover:text-white text-red-500 border-2 border-red-200 hover:border-red-500 text-sm font-semibold rounded-lg transition-all duration-200 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                    Supprimer
                                </button>
                            </form>
                        @endif

                    </div>

                </div>
            @endforeach
        </div>

        {{-- PAGINATION --}}
        <div class="mt-10 flex justify-center">
            {{ $signalements->onEachSide(1)->links() }}
        </div>
    </div>
</div>

@endsection