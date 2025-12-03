@extends('layouts.app')

@section('title', 'Liste d’achat')

@section('content')

<div class="p-4 pt-24">

    <h1 class="text-3xl font-bold mb-6">Ma liste d’achat</h1>

    {{-- État vide --}}
    @if ($items->isEmpty())
       <x-empty-state 
           title="Votre liste d’achat est vide" 
           subtitle="Ajoutez des bouteilles à votre liste pour planifier vos achats futurs."
           actionLabel="Explorer le catalogue"
           actionUrl="{{ route('bouteille.catalogue') }}"
       />
    @endif


    {{-- Liste d’achat --}}
    @foreach ($items as $item)
    @php
        $b = $item->bouteilleCatalogue;
    @endphp

    <div class="bg-white shadow-md rounded-xl p-4 mb-4 flex items-center justify-between border border-gray-100">

        {{-- Infos bouteille --}}
        <div class="flex items-start gap-3">

            {{-- Photo réelle de la bouteille --}}
            <div class="rounded-lg w-14 h-14 bg-gray-50 flex items-center justify-center overflow-hidden shadow-sm">
                @if ($b->image)
                    <img src="{{ $b->image }}" 
                         alt="Image {{ $b->nom }}"
                         class="w-full h-full object-contain">
                @else
                    <x-dynamic-component 
                        :component="'lucide-wine'" 
                        class="w-7 h-7 text-primary opacity-60"
                    />
                @endif
            </div>

            {{-- Texte --}}
            <div>
                <p class="font-semibold text-gray-800 text-sm leading-tight">
                    {{ $b->nom }}
                </p>

                {{-- pays + format (corrigé) --}}
                <p class="text-xs text-gray-500 mt-1">
                    {{ $b->pays->nom ?? 'Pays inconnu' }} — 
                    {{ $b->volume ?? 'Format inconnu' }}
                </p>

                {{-- prix --}}
                <p class="text-xs text-gray-600 mt-1">
                    Prix : 
                    <span class="font-semibold">
                        {{ number_format($b->prix, 2, ',', ' ') }} $
                    </span>
                </p>

                {{-- quantité --}}
                <p class="text-xs text-gray-600">
                    Quantité : <span class="font-semibold">{{ $item->quantite }}</span>
                </p>

                {{-- sous-total --}}
                <p class="text-xs text-gray-700 mt-1">
                    Sous-total : 
                    <span class="font-semibold">
                        {{ number_format($b->prix * $item->quantite, 2, ',', ' ') }} $
                    </span>
                </p>
            </div>
        </div>

        {{-- Bouton supprimer --}}
        <form method="POST" action="{{ route('listeAchat.destroy', $item) }}">
            @csrf
            @method('DELETE')

            <button 
                class="flex items-center gap-1 text-red-600 text-sm font-semibold hover:text-red-700 transition"
            >
                <x-dynamic-component component="lucide-trash-2" class="w-4 h-4" />
                Supprimer
            </button>
        </form>

    </div>
    @endforeach


    {{-- Total (si items présents) --}}
    @if (!$items->isEmpty())
        @php
            $total = $items->sum(fn($i) => $i->quantite * ($i->bouteilleCatalogue->prix ?? 0));
        @endphp

        <div class="mt-8 bg-gray-200 text-gray-900 p-5 rounded-2xl shadow-md text-center">
    <p class="text-xl font-bold">
        Total estimé : {{ number_format($total, 2, ',', ' ') }} $
    </p>
</div>

    @endif

</div>

@endsection
