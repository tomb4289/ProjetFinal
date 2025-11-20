@props(['title'=>'titre', 'undertitle' => '', 'actionBtn' => false])
<header class="mt-header flex flex-wrap justify-between items-center">
<div>
    <h1 class="text-3xl font-bold font-heading text-heading">{{ $title }}</h1>
    @if ($undertitle !== '')
        <p class="text-sm text-text-muted">{{ $undertitle }}</p>
    @endif
</div>
    @if ($actionBtn === true)
        <x-setting-btn id="setting-btn"/>
    @endif
</header>