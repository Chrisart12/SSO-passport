<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /**
     * Redirige l'utilisateur vers l'écran d'inscription du serveur Passport.
     *
     * Mécaniquement identique à LoginController::login() : même state
     * anti-CSRF, même redirect_uri, même callback. Seul screen_hint change,
     * pour indiquer à Passport d'afficher /register plutôt que /login.
     * Le reste du flow (échange du code, création de la session Sanctum,
     * persistance des tokens) est déjà géré par
     * LoginController::handleCallback — inutile de le dupliquer ici,
     * OAuth ne distingue pas "je viens de me connecter" de
     * "je viens de m'inscrire", les deux convergent sur le même échange
     * authorization_code.
     */
    public function register(Request $request)
    {
        // dd("eeeeeee");
        $request->session()->put('state', $state = Str::random(40));

        $query = http_build_query([
            'client_id'     => config('custom.oauth.client_id'),
            'redirect_uri'  => config('custom.oauth.redirect_uri'),
            'response_type' => 'code',
            'scope'         => null,
            'state'         => $state,
            // Paramètre custom, à lire côté Passport (avant l'authentification)
            // pour rediriger vers route('register') plutôt que route('login')
            // quand l'utilisateur n'est pas encore connecté.
            'screen_hint'   => 'register',
        ]);

        return redirect(config('custom.oauth_url') . '/oauth/authorize?' . $query);
    }

}