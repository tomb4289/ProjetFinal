@extends('layouts.app')
@section('title', 'Modifier Celliers')

{{-- Formulaire de modification d'un cellier --}}
@section('content')
    <section class="p-4 pt-2" aria-label="Modification du cellier">
        {{-- Insertion du composant d'en-tête de page --}}
        <x-page-header title="Modifier Celliers" />
        <div class="mt-6">
            {{-- Formulaire de modification d'un cellier --}}
            <form 
                action="{{ route('cellar.update', $cellier->id) }}" 
                method="POST" 
                class="flex flex-col gap-4"
                aria-label="Formulaire de mise à jour du cellier"
            >
                @csrf
                @method('PUT')
                <x-input label="Nom du cellier" name="nom" type="text" placeholder="Entrez le nom du cellier" value="{{ old('nom', $cellier->nom) }}" />
                <x-primary-btn label="Modifier le cellier" type="submit"/>
            </form>
        </div>
    </section>
@endsection