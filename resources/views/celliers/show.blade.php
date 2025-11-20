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

<<<<<<< HEAD
            <a
                href="{{ route('bouteilles.manuelles.create', $cellier) }}"
                class="bg-primary text-white font-bold py-2 px-4 rounded-lg hover:bg-primary-hover transition-colors duration-300 text-sm">
                Ajouter une bouteille
            </a>
        </div>

        {{-- Liste des bouteilles --}}
        <div class="bg-card border border-border-base rounded-xl shadow-md p-6">
            @if($cellier->bouteilles->isEmpty())
            <p class="text-text-muted">
                Ce cellier est encore vide. Utilisez le bouton « Ajouter une bouteille » pour commencer.
            </p>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($cellier->bouteilles as $bouteille)
                <div class="border border-border-base rounded-lg p-4 flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <h2 class="font-semibold text-text-title">
                            {{ $bouteille->nom }}
                        </h2>

                        {{-- Badge + boutons quantité --}}
                        <div class="flex items-center gap-2 bottle" data-id="{{ $bouteille->id }}">
                            <button class="btn-minus bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">−</button>
                            <span class="inline-flex items-center justify-center rounded-full bg-primary text-white text-xs px-2 py-0.5">
                                 {{ $bouteille->quantite }}
                            </span>
                            <button class="btn-plus bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded">+</button>
                        </div>
                    </div>

                    <div class="text-sm text-text-muted space-y-1">
                        @if($bouteille->pays)
                        <p><span class="font-medium text-text-body">Pays :</span> {{ $bouteille->pays }}</p>
                        @endif

                        @if($bouteille->format)
                        <p><span class="font-medium text-text-body">Format :</span> {{ $bouteille->format }}</p>
                        @endif

                        @if($bouteille->prix !== null)
                        <p><span class="font-medium text-text-body">Prix :</span> {{ number_format($bouteille->prix, 2, ',', ' ') }} $</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
=======
        @endif

    </div>

</section>
<div id='addWineBtnContainer' class="fixed z-10 bottom-0 left-0 w-full p-4 pt-10 bg-card border border-border-base shadow-lg rounded-t-lg transform translate-y-full transition-transform duration-300">
    <span class="flex items-center justify-between mb-4"> 
       <h1 class="text-3xl text-heading font-heading">Ajouter un vin</h1> 
       <x-dynamic-component :component="'lucide-x'" id="closeAddWine" class="w-6 h-6"/>
    </span>
    <div class="flex flex-col gap-4">
    <x-icon-text-btn
        :href="route('bouteilles.manuelles.create', $cellier->id)"
        icon="wine"
        title="Explorer le catalogue SAQ"
        subtitle="Recherchez des vins répertoriés à la SAQ."
    />
    <x-icon-text-btn
        :href="route('bouteilles.manuelles.create', $cellier->id)"
        icon="notebook-pen"
        title="Ajouter Manuellement"
        subtitle="Pour les vins non répertoriés à la SAQ."
    />
>>>>>>> 0feb06a1bc3b207fa12faece2190f11f4d727606
    </div>
</div>
@endsection