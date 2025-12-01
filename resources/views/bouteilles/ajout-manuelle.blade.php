@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-background pt-24" role="formulaire-ajout-manuelle">
    {{-- Conteneur centré --}}
    <section class="p-4 sm:w-full max-w-4xl mx-auto" role="section-formulaire-ajout-manuelle">
        <div class="bg-card border border-border-base rounded-xl shadow-md p-6 space-y-6" >

            {{-- En-tête avec le composant de ton ami --}}
            <x-page-header 
                title="Ajout manuel d’une bouteille" 
                undertitle="Remplissez le formulaire ci-dessous pour ajouter une bouteille manuellement à votre cellier." 
            />

            {{-- Formulaire --}}
            <form
                id="form-bouteille-manuelle"
                method="POST"
                action="{{ route('bouteilles.manuelles.store', $cellier) }}"
                novalidate
                class="space-y-4 mt-5"
                role="form"
            >
                @csrf

                {{-- Nom (obligatoire) --}}
                <x-input
                    label="Nom de la bouteille"
                    name="nom"
                    :required="true"
                    placeholder="Ex : Château X"
                    value="{{ old('nom') }}"
                />

                {{-- Pays --}}
                <x-input
                    label="Pays"
                    name="pays"
                    placeholder="Ex : France"
                    value="{{ old('pays') }}"
                />

                {{-- Format --}}
                <x-input
                    label="Format"
                    name="format"
                    placeholder="Ex : 750 ml"
                    value="{{ old('format') }}"
                />

                {{-- Type --}}
                <x-input
                    label="Type"
                    name="type"
                    placeholder="Ex : Rouge"
                    value="{{ old('type') }}"
                />

                {{-- Millésime --}}
                <x-input
                    label="Millésime"
                    name="millesime"
                    type="number"
                    placeholder="Ex : 2018"
                    value="{{ old('millesime') }}"
                />

                {{-- Quantité (obligatoire) --}}
                <x-input
                    label="Quantité"
                    name="quantite"
                    type="number"
                    :required="true"
                    placeholder="Ex : 1"
                    value="{{ old('quantite') }}"
                />

                {{-- Prix (obligatoire, décimal) --}}
                <x-input
                    label="Prix"
                    name="prix"
                    type="number"
                    step="0.01"
                    :required="true"
                    placeholder="Ex : 12.50"
                    value="{{ old('prix') }}"
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
@endsection
