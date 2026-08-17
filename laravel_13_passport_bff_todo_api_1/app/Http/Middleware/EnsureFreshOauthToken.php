<?php

namespace App\Http\Middleware;

use App\Exceptions\ExpiredOauthSessionException;
use App\Models\OauthSession;
use App\Services\OauthTokenRefresher;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureFreshOauthToken
{
    public function __construct(
        private readonly OauthTokenRefresher $refresher
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $session = OauthSession::where('user_id', $user->id)
            ->where('session_id', $request->session()->getId())
            ->first();

        if (! $session) {
            Auth::logout();
            $request->session()->invalidate();

            abort(401, 'Session OAuth introuvable.');
        }

        try {
            $session = $this->refresher->ensureFreshToken($session);
        } catch (ExpiredOauthSessionException $e) {
            Auth::logout();
            $request->session()->invalidate();

            abort(401, $e->getMessage());
        }

        // Rend le token frais disponible aux controllers suivants
        // (ex: pour proxifier un appel vers l'API métier distante).
        $request->attributes->set('oauth_session', $session);

        return $next($request);
    }
}