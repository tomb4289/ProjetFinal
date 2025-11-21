@extends('layouts.app')

@section('title', 'Catalogue des bouteilles')
<section class="p-4">
<x-page-header title="Catalogue des bouteilles" />
<div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 mt-6">
    @foreach ($bouteilles as $bouteille)
        <x-bouteille-card-block :nom="$bouteille->nom" :image="$bouteille->image" :prix="$bouteille->prix" />
    @endforeach
</div>
</section>
@section('content')

@endsection