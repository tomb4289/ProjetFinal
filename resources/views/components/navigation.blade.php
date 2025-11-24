@props(['addCellarBtn' => false, 'addWineBtn' => false])
{{-- Barre de navigation principale --}}
<section class="w-full fixed bottom-0 left-0 flex flex-col gap-4 items-center">
   {{-- Permet l'affichage du bouton Ajouter un cellier --}}
   @if ($addCellarBtn == true)
      <div class="w-full max-w-md px-4 pointer-events-auto">
            <x-primary-btn type="href" label="+ Créer un nouveau cellier" route="cellar.create" class="w-full py-3 shadow-lg" />
       </div>
   @endif
   {{-- Permet l'affichage du bouton Ajouter un vin --}}
   @if ($addWineBtn == true)
      <div class="w-full flex justify-end px-4 pointer-events-auto">
         <button id="addWineToCellar" class="bg-primary text-card border border-border-base shadow-lg rounded-full p-6 hover:bg-primary-hover transition">
            <x-dynamic-component :component="'lucide-plus'" class="w-10 h-10"/>
         </button>
       </div>
   @endif
<nav class="w-full bg-card bg- border-t border-border-base shadow-sm flex justify-between">
   <x-nav-item label='Celliers' icon='wine' url="{{ route('cellar.index') }}" :active="request()->routeIs('cellar.*')" />
   <x-nav-item label='Explorer' icon='compass' url="{{ route('bouteille.catalogue') }}" :active="request()->routeIs('bouteille.catalogue')" />
   <x-nav-item label='Compte' icon='user' url='test' />
</nav>
</section>