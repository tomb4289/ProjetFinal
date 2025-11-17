<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Affiche la page d'authentification
     * en mode "inscription" (register).
     */
    public function showRegisterForm()
    {
        // On envoie à la vue une variable "mode" pour afficher le bon formulaire.
        return view('auth.auth', ['mode' => 'register']);
    }

    /**
     * Traite le formulaire d'inscription.
     */
    public function register(Request $request)
    {
        // 1) Validation des données envoyées par le formulaire.
        //    La règle "confirmed" attend un champ "password_confirmation".
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        // 2) Création de l'utilisateur en BD avec mot de passe hashé.
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // 3) Connexion automatique de l'utilisateur après inscription.
        Auth::login($user);

        // 4) Régénération de la session pour plus de sécurité.
        $request->session()->regenerate();

        // 5) Redirection vers la page principale 
        return redirect()
            ->route('celliers.index')
            ->with('success', 'Félicitations! Votre compte a été créé et vous êtes connecté.');
    }

    /**
     * Affiche la page d'authentification
     * en mode "connexion" (login).
     */
    public function showLoginForm()
    {
        // Même vue que pour l'inscription, mais avec le mode "login".
        return view('auth.auth', ['mode' => 'login']);
    }

    /**
     * Traite le formulaire de connexion.
     */
    public function login(Request $request)
    {
        // 1) Validation de base des champs.
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // 2) Tentative de connexion avec les identifiants fournis.
        if (Auth::attempt($credentials)) {
            // 3) Si connexion réussie, on régénère la session.
            $request->session()->regenerate();

            // 4) Redirection vers la page protégée,
            //     vers 'celliers.index' par défaut.
            return redirect()
                ->intended(route('celliers.index'))
                ->with('success', 'Connexion réussie.');
        }

        // 5) Si échec : retour au formulaire avec un message d'erreur.
        return back()
            ->withErrors([
                'email' => 'Les identifiants sont incorrects.',
            ])
            ->onlyInput('email'); // on garde l'email, pas le mot de passe
    }

    /**
     * Déconnecte l'utilisateur.
     */
    public function logout(Request $request)
    {
        // 1) Déconnexion de l'utilisateur.
        Auth::logout();

        // 2) Invalidation de la session en cours.
        $request->session()->invalidate();

        // 3) Régénération du token CSRF.
        $request->session()->regenerateToken();

        // 4) Redirection vers la page de connexion avec message de succès.
        return redirect()
            ->route('login.form')
            ->with('success', 'Vous êtes maintenant déconnecté.');
    }
}
