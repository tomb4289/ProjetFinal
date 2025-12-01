@extends('layouts.app')

@section('title', 'Modifier les notes de dégustation')

@section('content')
<div class="min-h-screen bg-background pt-24" role="region" aria-label="Modification des notes de dégustation">
    <section class="p-4 sm:w-full max-w-4xl mx-auto">
        <div class="bg-card border border-border-base rounded-xl shadow-md p-6 space-y-6">
            
            {{-- En-tête avec bouton retour --}}
            <div class="flex items-center justify-between mb-6">
                <x-page-header 
                    title="Évaluation et notes de dégustation" 
                    undertitle="Notez cette bouteille et ajoutez vos notes de dégustation" 
                />
                <x-back-btn :route="route('bouteilles.show', [$cellier, $bouteille])" />
            </div>

            {{-- Formulaire --}}
            <form
                method="POST"
                action="{{ route('bouteilles.note.update', [$cellier, $bouteille]) }}"
                class="space-y-4"
                aria-label="Formulaire d'évaluation de la bouteille"
            >
                @csrf
                @method('PUT')

                {{-- Champ notation par étoiles --}}
                <div role="group" aria-labelledby="label-rating">
                    <label id="label-rating" class="block text-sm font-medium text-text-body mb-2">
                        Note (étoiles)
                    </label>
                    {{-- Composant intact --}}
                    <x-star-rating 
                        :rating="old('rating', $bouteille->rating)" 
                        :editable="true"
                        name="rating"
                    />
                    @error('rating')
                        <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-text-muted" id="help-rating">
                        Cliquez sur les étoiles pour noter de 1 à 5
                    </p>
                </div>

                {{-- Champ notes de dégustation --}}
                <div>
                    <label for="note_degustation" class="block text-sm font-medium text-text-body mb-2">
                        Notes de dégustation
                    </label>
                    <textarea
                        id="note_degustation"
                        name="note_degustation"
                        rows="10"
                        class="w-full px-4 py-2 border border-border-base rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent resize-y"
                        placeholder="Décrivez vos impressions, arômes, saveurs, texture, etc..."
                        aria-describedby="help-note"
                    >{{ old('note_degustation', $bouteille->note_degustation) }}</textarea>
                    @error('note_degustation')
                        <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-text-muted" id="help-note">
                        Maximum 5000 caractères
                    </p>
                </div>

                {{-- Messages de succès --}}
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg" role="status">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Actions --}}
                <div class="flex gap-3 pt-4">
                    <x-primary-btn
                        label="Enregistrer les notes"
                        type="submit"
                    />
                    <x-back-btn 
                        :route="route('bouteilles.show', [$cellier, $bouteille])" 
                        label="Annuler"
                    />
                </div>
            </form>
        </div>
    </section>
</div>
@endsection