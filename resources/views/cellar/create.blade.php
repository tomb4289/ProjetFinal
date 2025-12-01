@extends('layouts.app')
@section('title', 'Ajout Celliers')

{{-- Formulaire d'ajout d'un nouveau cellier --}}
@section('content')
    <section class="p-4 pt-2" aria-label="Ajout d'un nouveau cellier">
        {{-- Insertion du composant d'en-tête de page --}}
        <x-page-header title="Ajout Celliers" />
        <div class="mt-6">
            <form 
                action="{{ route('cellar.store') }}" 
                method="POST" 
                class="flex flex-col gap-4"
                aria-label="Formulaire de création de cellier"
            >
                @csrf
                <x-input label="Nom du cellier" name="nom" type="text" placeholder="Entrez le nom du cellier" />
                <x-primary-btn label="Ajouter le cellier" type="submit" />
            </form>
        </div>
    </section>
@endsection