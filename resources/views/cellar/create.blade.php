@extends('layouts.app')
@section('title', 'Ajout Celliers')


@section('content')
    <section class="p-4 pt-2">
        <x-page-header title="Ajout Celliers" />
        <div class="mt-6">
            <form action="{{ route('cellar.store') }}" method="POST" class="flex flex-col gap-4">
                @csrf
                <x-input label="Nom du cellier" name="nom" type="text" placeholder="Entrez le nom du cellier" />
                <x-primary-btn label="Ajouter le cellier" type="submit" />
            </form>
    </section>
@endsection