@extends('layouts.app')
@section('title', 'Mon cellier – ' . $cellier->nom)

{{-- Bouton "Ajouter une bouteille" --}}
@section('add-wine-btn', '')

@section('content')
<section class="p-4 pt-2">

    <x-page-header 
        :title="$cellier->nom" 
        undertitle="Vue principale du cellier – liste des bouteilles." 
    />

    <div class="mt-6">

        @if($cellier->bouteilles->isEmpty())

            <p class="text-text-muted">
                Ce cellier est encore vide. Utilisez le bouton « Ajouter une bouteille » pour commencer.
            </p>

        @else

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">

                @foreach($cellier->bouteilles as $bouteille)

                    <div class="border border-border-base rounded-lg p-4 flex flex-col gap-3 bg-card">

                        {{-- Nom + quantité --}}
                        <div class="flex items-center justify-between">
                            <h2 class="font-semibold text-text-title">
                                {{ $bouteille->nom }}
                            </h2>

                            <span class="inline-flex items-center justify-center rounded-full 
                                bg-primary text-white text-xs px-2 py-0.5">
                                x {{ $bouteille->quantite }}
                            </span>
                        </div>

                        {{-- Infos --}}
                        <div class="text-sm text-text-muted space-y-1">
                            @if($bouteille->pays)
                                <p><span class="font-medium text-text-body">Pays :</span> {{ $bouteille->pays }}</p>
                            @endif

                            @if($bouteille->format)
                                <p><span class="font-medium text-text-body">Format :</span> {{ $bouteille->format }}</p>
                            @endif

                            @if($bouteille->prix !== null)
                                <p><span class="font-medium text-text-body">Prix :</span> 
                                    {{ number_format($bouteille->prix, 2, ',', ' ') }} $
                                </p>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-2 mt-auto">

                            {{-- Bouton supprimer --}}
                            <x-delete-btn 
                                :route="route('bouteilles.delete', [
                                    'cellier' => $cellier->id,
                                    'bouteille' => $bouteille->id
                                ])"
                            />

                            {{-- Bouton modifier (seulement si bouteille manuelle) --}}
                            @if ($bouteille->code_saq === null)
                                <x-edit-btn
                                    :route="route('bouteilles.edit', [$cellier->id, $bouteille->id])"
                                    label="Modifier"
                                />
                            @endif

                        </div>
                    </div>

                @endforeach

            </div>

        @endif

    </div>

</section>
@endsection
