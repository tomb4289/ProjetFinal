@extends('layouts.app')
@section('title', 'Modifier Celliers')


@section('content')
    <section class="p-4 pt-2">
        <x-page-header title="Modifier Celliers" />
        <div class="mt-6">
            <form action="#" method="POST" class="flex flex-col gap-4">
                @csrf
                <x-input label="Nom du cellier" name="cellar_name" type="text" placeholder="Entrez le nom du cellier" />
                <x-primary-btn label="Modifier le cellier" type="submit" class="w-full py-3" />
            </form>
    </section>
@endsection