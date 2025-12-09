<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cellier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Contrôleur pour la gestion de l'authentification.
 * 
 * Ce contrôleur gère toutes les opérations liées à l'authentification :
 * connexion, inscription et déconnexion des utilisateurs.
 * 
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * Affiche la page d'authentification en mode "inscription" (register).
     * 
     * @return View La vue du formulaire d'inscription
     */
    public function showRegisterForm(): View
    {
        // On envoie à la vue une variable "mode" pour afficher le bon formulaire.
        return view('auth.auth', ['mode' => 'register']);
    }

    /**
     * Traite le formulaire d'inscription et crée un nouveau compte utilisateur.
     * 
     * Valide les données du formulaire, crée l'utilisateur en base de données,
     * connecte automatiquement l'utilisateur et redirige vers la page des celliers.
     * 
     * @param Request $request La requête HTTP contenant les données du formulaire
     * @return RedirectResponse Redirection vers la page des celliers avec un message de succès
     */
    public function register(Request $request): RedirectResponse
    {
        // 1) Validation des données envoyées par le formulaire.
        //    La règle "confirmed" attend un champ "password_confirmation".
        $validated = $request->validate(
            [
                'name'     => 'required|string|min:3|max:255',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
            ],
            [
                'email.email' => 'Le format du courriel est invalide.',
                'email.unique' => 'Ce courriel est déjà utilisé pour un autre compte.',
                'password.confirmed' => 'Le mot de passe de confirmation ne correspond pas.',
            ]
        );

        // 2) Création de l'utilisateur en BD avec mot de passe hashé.
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        // 2.5) Création d'un cellier par défaut pour le nouvel utilisateur.
        Cellier::create([
            'nom'    => 'Mon cellier',
            'user_id' => $user->id,
        ]);

        // 3) Connexion automatique de l'utilisateur après inscription.
        Auth::login($user);

        // 4) Régénération de la session pour plus de sécurité.
        $request->session()->regenerate();

        // 5) Redirection vers la page des celliers 
        return redirect()
            ->route('cellar.index')
            ->with('success', 'Félicitations! Votre compte a été créé et vous êtes connecté.');
    }

    /**
     * Affiche la page d'authentification en mode "connexion" (login).
     * 
     * @return View La vue du formulaire de connexion
     */
    public function showLoginForm(): View
    {
        // Même vue que pour l'inscription, mais avec le mode "login" pour afficher directement le formulaire 'connexion'.
        return view('auth.auth', ['mode' => 'login']);
    }

    /**
     * Traite le formulaire de connexion et authentifie l'utilisateur.
     * 
     * Valide les identifiants fournis, tente de connecter l'utilisateur
     * et redirige vers la page des celliers en cas de succès.
     * 
     * @param Request $request La requête HTTP contenant les identifiants (email et mot de passe)
     * @return RedirectResponse Redirection vers la page des celliers en cas de succès, 
     *                         ou retour au formulaire avec erreur en cas d'échec
     */
    public function login(Request $request): RedirectResponse
    {
        // Validation
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Récupérer l'utilisateur
        $user = User::where('email', $credentials['email'])->first();

        // Si le compte existe mais est désactivé
        if ($user && ! $user->is_active) {
            return back()
                ->with('error', 'Votre compte a été désactivé. Veuillez contacter un administrateur.')
                ->onlyInput('email');
        }

        // Tentative de connexion
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            /** @var \App\Models\User $user */
            $user = Auth::user();
            $user->last_login_at = now();   
            $user->save();                  


            return redirect()
                ->intended(route('cellar.index'))
                ->with('success', 'Connexion réussie.');
        }

        // Mauvais identifiants
        return back()
            ->withErrors([
                'email' => 'Les identifiants sont incorrects.',
            ])
            ->onlyInput('email');
    }


    /**
     * Déconnecte l'utilisateur actuellement connecté.
     * 
     * Déconnecte l'utilisateur, invalide la session en cours,
     * régénère le token CSRF et redirige vers la page de connexion.
     * 
     * @param Request $request La requête HTTP
     * @return RedirectResponse Redirection vers la page de connexion avec un message de succès
     */
    public function logout(Request $request): RedirectResponse
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
