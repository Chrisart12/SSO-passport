<?php

namespace App\Services;

use App\Exceptions\ExpiredOauthSessionException;
use App\Models\OauthSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OauthTokenRefresher
{
    /**
     * Retourne une session avec un access_token valide, en le rafraîchissant
     * si nécessaire. Protégé par un verrou pour éviter que deux requêtes
     * concurrentes ne déclenchent deux refresh en parallèle (ce qui invaliderait
     * l'un des deux refresh_token, Passport les faisant tourner à chaque usage).
     */
    public function ensureFreshToken(OauthSession $session): OauthSession
    {
        if (! $session->isExpired()) {
            return $session;
        }

        $lock = Cache::lock('oauth-refresh:' . $session->id, 10);

        try {
            $lock->block(5);

            // On recharge la session : une autre requête a peut-être déjà
            // rafraîchi le token pendant qu'on attendait le verrou.
            $session->refresh();

            if (! $session->isExpired()) {
                return $session;
            }

            return $this->refresh($session);
        } finally {
            optional($lock)->release();
        }
    }

    private function refresh(OauthSession $session): OauthSession
    {
        $response = Http::asForm()->post(config('custom.oauth_url') . '/oauth/token', [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $session->refresh_token,
            'client_id'     => config('custom.oauth.client_id'),
            'client_secret' => config('custom.oauth.client_secret'),
        ]);

        if ($response->failed()) {
            Log::warning('Échec du refresh OAuth', [
                'user_id' => $session->user_id,
                'status'  => $response->status(),
            ]);

            // Le refresh token n'est plus valide : la session ne peut pas être
            // restaurée automatiquement, l'utilisateur doit se reconnecter.
            $session->delete();

            throw new ExpiredOauthSessionException();
        }

        $tokens = $response->json();

        $session->update([
            'access_token'     => $tokens['access_token'],
            // Passport fait tourner le refresh token à chaque utilisation :
            // il faut toujours le remplacer, jamais garder l'ancien.
            'refresh_token'    => $tokens['refresh_token'],
            'expires_at'       => now()->addSeconds($tokens['expires_in']),
            'last_activity_at' => now(),
        ]);

        return $session;
    }
}