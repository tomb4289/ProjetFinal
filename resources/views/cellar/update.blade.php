@extends('layouts.app')
@section('title', 'Modifier Celliers')


@section('content')
    <section class="p-4 pt-2">
        <x-page-header title="Modifier Celliers" />
        <div class="mt-6">
            <form action="{{ route('cellar.update', $cellier->id) }}" method="POST" class="flex flex-col gap-4">
                @csrf
                @method('PUT')
                <x-input label="Nom du cellier" name="nom" type="text" placeholder="Entrez le nom du cellier" value="{{ old('nom', $cellier->nom) }}" />
                <x-primary-btn label="Modifier le cellier" type="submit"/>
            </form>
    </section>
@endsection