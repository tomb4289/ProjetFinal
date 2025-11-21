@props(['title'=>'titre', 'undertitle' => '', 'actionBtn' => false])
{{-- En-tête de page --}}
<header class="mt-header flex flex-wrap justify-between items-center">
<div>
    <h1 class="text-3xl font-bold font-heading text-heading">{{ $title }}</h1>
    {{-- Sous-titre de la page si présent --}}
    @if ($undertitle !== '')
        <p class="text-sm text-text-muted">{{ $undertitle }}</p>
    @endif
</div>

    {{-- Bouton d'action si demandé --}}
    @if ($actionBtn === true)
        <x-setting-btn id="setting-btn"/>
    @endif
</header>