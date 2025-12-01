@extends('layouts.app') 

@section('content')
{{-- Formulaire de modification manuelle d'une bouteille --}}
<div class="min-h-screen bg-background pt-24" role="region" aria-label="Modification manuelle d'une bouteille">
    <section class="p-4 sm:w-full max-w-4xl mx-auto">
        <div class="bg-card border border-border-base rounded-xl shadow-md p-6 space-y-6">

            {{-- En-tête --}}
            <x-page-header 
                title="Modification manuelle d’une bouteille" 
                undertitle="Modifiez les informations de votre bouteille manuelle." 
            />

            {{-- Formulaire  --}}
            <form
                id="form-bouteille-manuelle"
                method="POST"
                action="{{ route('bouteilles.update', [$cellier, $bouteille]) }}"
                novalidate
                class="space-y-4 mt-5"
                aria-label="Formulaire de modification des détails de la bouteille"
            >
                @csrf
                @method('PUT')

                {{-- Nom (obligatoire) --}}
                <x-input
                    label="Nom de la bouteille"
                    name="nom"
                    :required="true"
                    placeholder="Ex : Château X"
                    value="{{ old('nom', $bouteille->nom) }}"
                />

                {{-- Pays --}}
                <x-input
                    label="Pays"
                    name="pays"
                    placeholder="Ex : France"
                    value="{{ old('pays', $bouteille->pays) }}"
                />

                {{-- Format --}}
                <x-input
                    label="Format"
                    name="format"
                    placeholder="Ex : 750 ml"
                    value="{{ old('format', $bouteille->format) }}"
                />

                {{-- Type --}}
                <x-input
                    label="Type"
                    name="type"
                    placeholder="Ex : Rouge"
                    value="{{ old('type', $bouteille->type) }}"
                />

                {{-- Millésime --}}
                <x-input
                    label="Millésime"
                    name="millesime"
                    type="number"
                    placeholder="Ex : 2018"
                    value="{{ old('millesime', $bouteille->millesime) }}"
                />

                {{-- Quantité (obligatoire) --}}
                <x-input
                    label="Quantité"
                    name="quantite"
                    type="number"
                    :required="true"
                    placeholder="Ex : 1"
                    value="{{ old('quantite', $bouteille->quantite) }}"
                />

                {{-- Prix (obligatoire, décimal) --}}
                <x-input
                    label="Prix"
                    name="prix"
                    type="number"
                    step="0.01"
                    :required="true"
                    placeholder="Ex : 12.50"
                    value="{{ old('prix', $bouteille->prix) }}"
                />

                <div class="pt-4 flex justify-between">
                    <x-primary-btn
                        label="Modifier la bouteille"
                        type="submit"
                        id="btn-submit-bouteille"
                    />
                </div>
            </form>
        </div>
    </section>
</div>
@endsection