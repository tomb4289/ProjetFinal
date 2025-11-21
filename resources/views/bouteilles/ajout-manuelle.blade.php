@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-background pt-24">
    {{-- Conteneur centré --}}
    <section class="p-4 sm:w-full max-w-4xl mx-auto">
        <div class="bg-card border border-border-base rounded-xl shadow-md p-6 space-y-6">

            {{-- En-tête avec le composant de ton ami --}}
            <x-page-header 
                title="Ajout manuel d’une bouteille" 
                undertitle="Remplissez le formulaire ci-dessous pour ajouter une bouteille manuellement à votre cellier." 
            />

            {{-- Formulaire (TON code, gardé tel quel) --}}
            <form
                id="form-bouteille-manuelle"
                method="POST"
                action="{{ route('bouteilles.manuelles.store', $cellier) }}"
                novalidate
                class="space-y-4 mt-5"
            >
                @csrf

                {{-- Nom (obligatoire) --}}
                <x-input
                    label="Nom de la bouteille"
                    name="nom"
                    :required="true"
                    placeholder="Ex : Château X"
                />

                {{-- Pays --}}
                <x-input
                    label="Pays"
                    name="pays"
                    placeholder="Ex : France"
                />

                {{-- Format --}}
                <x-input
                    label="Format"
                    name="format"
                    placeholder="Ex : 750 ml"
                />

                {{-- Quantité (obligatoire) --}}
                <x-input
                    label="Quantité"
                    name="quantite"
                    type="number"
                    :required="true"
                    placeholder="Ex : 1"
                />

                {{-- Prix (obligatoire, décimal) --}}
                <x-input
                    label="Prix"
                    name="prix"
                    type="text"
                    :required="true"
                    placeholder="Ex : 12.50"
                />

                <div class="pt-4 flex justify-between">
                    <x-primary-btn
                        label="Ajouter la bouteille"
                        type="submit"
                        id="btn-submit-bouteille"
                    />
                </div>
            </form>
        </div>
    </section>
</div>

{{-- ton script JS de validation reste pareil --}}
@endsection
