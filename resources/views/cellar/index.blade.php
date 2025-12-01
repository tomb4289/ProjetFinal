@extends('layouts.app')
@section('title', 'Mes Celliers')

{{-- Ajoute le bouton Ajouter un cellier. Voir app.blade.php --}}
@section('add-cellar-btn', '')

@section('content')
    <section class="p-4 pt-2" aria-label="Gestion de mes celliers">
        {{-- N'affiche pas le bouton modifier si aucun cellier --}}
        <x-page-header title="Mes Celliers" :actionBtn="$celliers->isNotEmpty()" />
        
        <div 
            class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3" 
            role="list" 
            aria-label="Liste des celliers"
        >
            @forelse ($celliers as $cellier)
                {{-- Note : Le composant doit idéalement avoir un role="listitem" en interne --}}
                <x-cellar-box 
                    :name="$cellier->nom" 
                    :amount="$cellier->bouteilles->count()" 
                    :id="$cellier->id"
                />
            {{-- Si vide, affiche un message --}}
            @empty
                <div class="col-span-full p-6 text-center text-text-muted bg-card rounded-lg border border-border-base" role="status">
                    Vous n'avez aucun cellier pour le moment.
                </div>
            @endforelse
        </div>
        
    </section>
@endsection