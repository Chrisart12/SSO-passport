<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CaptureOauthScreenHint
{
    /**
     * Mémorise screen_hint (ex: "register") en session AVANT que le
     * middleware `auth` n'intercepte la requête et ne redirige vers
     * route('login') ou route('register'). Sans ça, le paramètre serait
     * perdu : la requête vers /oauth/authorize est interrompue par la
     * redirection, screen_hint ne redescend jamais jusqu'au formulaire.
     */
    public function handle(Request $request, Closure $next)
    {
       
        // Ne touche à la session que sur la route d'autorisation elle-même :
        // évite d'effacer le hint sur les requêtes suivantes (login, register)
        // qui n'ont plus screen_hint dans leur query string.
        if ($request->is('oauth/authorize')) {
            if ($request->filled('screen_hint')) {
                $request->session()->put('oauth_screen_hint', $request->query('screen_hint'));
            } else {
                $request->session()->forget('oauth_screen_hint');
            }
        }

        return $next($request);
    }
}