@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
        {{-- Section d'affichage des bouteilles du catalogue --}}
        @if($bouteilles->isNotEmpty())
            <section class="w-full mt-12 mb-8 px-4">
                <h2 class="text-xl font-bold text-text-body mb-6 text-center">Bouteilles du catalogue</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($bouteilles as $bouteille)
                        <x-bouteille-card
                            :id="$bouteille->id"
                            :nom="$bouteille->nom"
                            :type="$bouteille->typeVin?->nom"
                            :millesime="$bouteille->millesime"
                            :urlImage="$bouteille->url_image"
                            :pays="$bouteille->pays?->nom"
                            :region="$bouteille->region?->nom"
                            :volume="$bouteille->volume"
                            :prix="$bouteille->prix"
                            :codeSaq="$bouteille->code_saQ"
                        />
                    @endforeach
                </div>
            </section>
        @else
            <section class="w-full mt-12 mb-8 px-4">
                <div class="bg-card border border-border-base rounded-lg p-8 text-center">
                    <p class="text-muted text-lg">Aucune bouteille dans le catalogue pour le moment.</p>
                    <p class="text-muted text-sm mt-2">Utilisez <code class="bg-gray-100 px-2 py-1 rounded">php artisan saq:import</code> pour importer des produits.</p>
                </div>
            </section>
        @endif  
@endsection