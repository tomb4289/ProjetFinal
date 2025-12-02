@extends('layouts.app')

@section('title', 'Liste d’achat')

@section('content')
<h1 class="text-2xl font-bold mb-4">Ma liste d’achat</h1>

@foreach ($items as $item)
<div class="p-4 bg-card shadow rounded mb-3 flex justify-between">
    <div>
        <p>{{ $item->bouteilleCatalogue->nom }}</p>
        <p>Quantité : {{ $item->quantite }}</p>
    </div>

    <form method="POST" action="{{ route('liste_achat.destroy', $item) }}">
        @csrf @method('DELETE')
        <button class="text-red-500">Supprimer</button>
    </form>
</div>
@endforeach
@endsection