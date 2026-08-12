<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OauthLogoutController extends Controller
{
    /**
     * Logout accessible par simple navigation (GET), pour être appelé
     * depuis un window.location.href côté front après la fermeture de
     * la session BFF — la route /logout standard de Breeze est en POST
     * + CSRF, donc inutilisable pour une redirection cross-domaine directe.
     */
    public function __invoke(Request $request): RedirectResponse
    {

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();


        $redirectUri = $request->query('redirect_uri');

        // Ne jamais rediriger vers une URL arbitraire fournie en query string
        // (open redirect) : on n'accepte que les domaines des apps clientes
        // connues, listés en config.
        if ($redirectUri && $this->isAllowedRedirect($redirectUri)) {
            return redirect()->away($redirectUri);
        }

        return redirect()->route('login');
    }

    private function isAllowedRedirect(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);
        $port = parse_url($url, PHP_URL_PORT);
 
        // parse_url(..., PHP_URL_HOST) ne retourne jamais le port séparément :
        // on le rajoute nous-même pour matcher le format "host:port" attendu
        // en config quand un port est présent (cas du dev en localhost:5173).
        $hostWithPort = $port ? "{$host}:{$port}" : $host;
 
        return in_array($hostWithPort, config('custom.allowed_logout_redirect_hosts', []), true);
    }

}