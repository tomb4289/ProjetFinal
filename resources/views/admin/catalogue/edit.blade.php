@extends('layouts.app')

@section('title', 'Modifier la bouteille du catalogue')

@section('content')
<div class="min-h-screen bg-background pt-24" role="region" aria-label="Modification d'une bouteille du catalogue">
    <section class="p-4 sm:w-full max-w-4xl mx-auto">
        <div class="bg-card border border-border-base rounded-xl shadow-md p-6 space-y-6">

            {{-- Lien retour --}}
            <div class="mb-4">
                <x-back-btn 
                    :route="route('catalogue.show', $bouteille->id)" 
                    label="Retour aux détails" 
                />
            </div>

            {{-- En-tête --}}
            <x-page-header 
                title="Modifier la bouteille du catalogue" 
                undertitle="Modifiez les informations de la bouteille (ex: prix, nom, etc.)" 
            />

            {{-- Formulaire  --}}
            <form
                method="POST"
                action="{{ route('admin.catalogue.update', $bouteille) }}"
                novalidate
                class="space-y-4 mt-5"
                aria-label="Formulaire de modification de la bouteille du catalogue"
            >
                @csrf
                @method('PUT')

                {{-- Nom (obligatoire) --}}
                <x-input
                    label="Nom de la bouteille"
                    name="nom"
                    :required="true"
                    placeholder="Ex : Château X"
                    value="{{ old('nom', $bouteille->nom) }}"
                />

                {{-- Prix (obligatoire) --}}
                <x-input
                    label="Prix"
                    name="prix"
                    type="number"
                    step="0.01"
                    :required="true"
                    placeholder="Ex : 12.50"
                    value="{{ old('prix', $bouteille->prix) }}"
                />

                {{-- Millésime --}}
                <x-input
                    label="Millésime"
                    name="millesime"
                    type="number"
                    placeholder="Ex : 2018"
                    value="{{ old('millesime', $bouteille->millesime) }}"
                />

                {{-- Volume --}}
                <x-input
                    label="Volume"
                    name="volume"
                    placeholder="Ex : 750 ml"
                    value="{{ old('volume', $bouteille->volume) }}"
                />

                {{-- Pays --}}
                <div>
                    <label for="id_pays" class="block text-sm font-medium text-heading mb-1.5">
                        Pays
                    </label>
                    <select 
                        name="id_pays" 
                        id="id_pays"
                        class="block w-full rounded-lg border-border-base bg-neutral-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                    >
                        <option value="">Sélectionner un pays</option>
                        @foreach($pays as $p)
                            <option value="{{ $p->id }}" {{ old('id_pays', $bouteille->id_pays) == $p->id ? 'selected' : '' }}>
                                {{ $p->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_pays')
                        <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Type de vin --}}
                <div>
                    <label for="id_type_vin" class="block text-sm font-medium text-heading mb-1.5">
                        Type de vin
                    </label>
                    <select 
                        name="id_type_vin" 
                        id="id_type_vin"
                        class="block w-full rounded-lg border-border-base bg-neutral-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                    >
                        <option value="">Sélectionner un type</option>
                        @foreach($types as $t)
                            <option value="{{ $t->id }}" {{ old('id_type_vin', $bouteille->id_type_vin) == $t->id ? 'selected' : '' }}>
                                {{ $t->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_type_vin')
                        <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Région --}}
                <div>
                    <label for="id_region" class="block text-sm font-medium text-heading mb-1.5">
                        Région
                    </label>
                    <select 
                        name="id_region" 
                        id="id_region"
                        class="block w-full rounded-lg border-border-base bg-neutral-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm py-2.5 px-3"
                    >
                        <option value="">Sélectionner une région</option>
                        @foreach($regions as $r)
                            <option value="{{ $r->id }}" {{ old('id_region', $bouteille->id_region) == $r->id ? 'selected' : '' }}>
                                {{ $r->nom }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_region')
                        <p class="mt-1 text-sm text-danger">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Code SAQ --}}
                <x-input
                    label="Code SAQ"
                    name="code_saQ"
                    placeholder="Ex : 12345"
                    value="{{ old('code_saQ', $bouteille->code_saQ) }}"
                />

                {{-- URL SAQ --}}
                <x-input
                    label="URL SAQ"
                    name="url_saq"
                    type="url"
                    placeholder="Ex : https://www.saq.com/..."
                    value="{{ old('url_saq', $bouteille->url_saq) }}"
                />

                <div class="pt-4 flex justify-between">
                    <x-primary-btn
                        label="Mettre à jour la bouteille"
                        type="submit"
                        id="btn-submit-bouteille"
                    />
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

