@extends('layouts.app')

@section('content')

<div class="mt-12 py-8 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-3xl mx-auto">
        
        
        <nav class="flex mb-5 text-sm text-gray-500" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.signalements.index') }}" class="hover:text-primary transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Retour aux signalements
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 text-gray-400 font-medium">Détails</span>
                    </div>
                </li>
            </ol>
        </nav>


        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            

            <div class="bg-gray-50 px-6 py-5 border-b border-gray-100 sm:flex sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
 
                    <div class="p-2 bg-white rounded-lg border border-gray-200 shadow-sm hidden sm:block">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 leading-6">Détail du signalement</h1>
                        <p class="text-sm text-gray-500 mt-1">ID #{{ $signalement->id }}</p>
                    </div>
                </div>

                <div class="mt-4 sm:mt-0 flex items-center">
                    @if (!$signalement->is_read)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10">
                            <span class="w-2 h-2 mr-2 bg-red-600 rounded-full animate-pulse"></span>
                            Nouveau
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Traité / Lu
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-6 sm:p-8 space-y-8">

              
                <div class="relative group">
                    <div class="absolute -inset-0 rounded-lg blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                    <div class="relative bg-white border border-gray-100 rounded-lg p-4 sm:flex sm:justify-between sm:items-center">
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Bouteille concernée</p>
                            <h3 class="text-lg font-bold text-gray-900">{{ $signalement->bouteilleCatalogue->nom }}</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Code SAQ / Référence si dispo</p>
                        </div>
                        
                        <div class="mt-3 sm:mt-0">
                            <a href="{{ route('catalogue.show', $signalement->bouteilleCatalogue) }}" class="text-sm font-medium text-primary hover:text-primary-active flex items-center">
                                Voir la fiche
                                <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>

                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <div class="md:col-span-1 space-y-4">
                        <div>
                            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wide">Type de problème</span>
                            <span class="mt-1 block text-sm font-medium text-gray-900 bg-gray-100 px-2.5 py-1 rounded-md inline-block">
                                {{ $signalement->nom }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wide">Signalé le</span>
                            <div class="mt-1 flex items-center text-sm text-gray-700 font-medium">
                                <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ $signalement->created_at->translatedFormat('d F Y') }}
                            </div>
                            <div class="mt-1 flex items-center text-xs text-gray-500 ml-5">
                                à {{ $signalement->created_at->format('H:i') }}
                            </div>
                        </div>
                    </div>

                    
                    <div class="md:col-span-2">
                        <span class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Description détaillée</span>
                       
                        <div class="bg-gray-50 rounded-lg p-2 border border-gray-100 text-gray-700 text-sm leading-relaxed break-words">
                            {{ $signalement->description }}
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:justify-end gap-3">
                
               
                <a href="{{ route('admin.signalements.index') }}" 
                   class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50  transition-colors order-2 sm:order-1">
                    Retour
                </a>

                
                @if (!$signalement->is_read)
                    <form action="{{ route('admin.signalements.read', $signalement) }}" method="POST" class="w-full sm:w-auto order-1 sm:order-2">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 text-sm font-medium rounded-lg shadow-sm text-primary bg-button-default hover:bg-primary hover:text-white border-2 border-primary transition-colors">
                            <svg class="mr-2 -ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Marquer comme traité
                        </button>
                    </form>
                @else
                   
                    <button disabled class="w-full sm:w-auto order-1 sm:order-2 inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-gray-400 bg-gray-100 cursor-not-allowed">
                        <svg class="mr-2 -ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Déjà traité
                    </button>
                @endif
            </div>

        </div>
    </div>
</div>

@endsection