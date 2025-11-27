{{-- Formulaire de connexion --}}
<form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4 {{ $attributes->get('class') }}" id="{{ $attributes->get('id') }}">
    @csrf
    <x-input 
        label="Adresse courriel" 
        name="email" 
        type="email" 
        placeholder="Entrez votre adresse courriel"
    />

    <x-input 
        label="Mot de passe" 
        name="password" 
        type="password" 
        placeholder="Entrez votre mot de passe"
    />

    <x-primary-btn type="submit" label="Connexion" />
</form>