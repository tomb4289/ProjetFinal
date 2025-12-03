@props(['addCellarBtn' => false, 'addWineBtn' => false])
{{-- Barre de navigation principale --}}
<section class="w-full fixed bottom-0 left-0 flex flex-col gap-4 items-center z-10" aria-label="Barre d'actions et de navigation">
   {{-- Permet l'affichage du bouton Ajouter un cellier --}}
   @if ($addCellarBtn == true)
   <div class="w-full max-w-md px-4 pointer-events-auto">
      <x-primary-btn type="href" label="+ Créer un nouveau cellier" route="cellar.create" class="w-full py-3 shadow-lg" />
   </div>
   @endif
   {{-- Permet l'affichage du bouton Ajouter un vin --}}
   @if ($addWineBtn == true)
      <div class="w-full flex justify-end px-4 pointer-events-auto">
         <button id="addWineToCellar" class="group bg-button-default text-card border-2 border-primary shadow-lg rounded-full p-4 hover:bg-button-hover transition">
            <x-dynamic-component :component="'lucide-plus'" class="w-10 h-10 stroke-primary group-hover:stroke-white transition"/>
         </button>
       </div>
   @endif
   <nav class="w-full bg-card bg- border-t border-border-base shadow-sm flex justify-between" aria-label="Menu principal">
      <x-nav-item label='Celliers' icon='wine' url="{{ route('cellar.index') }}" :active="request()->routeIs('cellar.*')" />
      <x-nav-item label='Explorer' icon='compass' url="{{ route('bouteille.catalogue') }}" :active="request()->routeIs('bouteille.catalogue')" />
      <x-nav-item label='Liste' icon='shopping-cart' url="{{ route('listeAchat.index') }}" :active="request()->routeIs('listeAchat.*')" />
      <x-nav-item label='Compte' icon='user' url="{{ route('profile.index') }}" :active="request()->routeIs('profile.*')" />

      {{-- Lien Admin : visible seulement pour les administrateurs --}}
      @auth
      @if(auth()->user()->is_admin)
      <x-nav-item
         label="Admin"
         icon="shield" {{-- ou 'settings', 'shield-check', etc. selon l’icône lucide que tu préfères --}}
         url="{{ route('admin.users.index') }}"
         :active="request()->routeIs('admin.*')" />
      @endif
      @endauth
   </nav>

</section>