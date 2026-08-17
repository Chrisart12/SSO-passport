<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\InvalidOauthStateException;
use App\Http\Controllers\Controller;
use App\Models\OauthSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Redirige l'utilisateur vers l'écran d'autorisation du serveur Passport.
     */
    public function login(Request $request)
    {
        $request->session()->put('state', $state = Str::random(40));

        $query = http_build_query([
            'client_id'     => config('custom.oauth.client_id'),
            'redirect_uri'  => config('custom.oauth.redirect_uri'),
            'response_type' => 'code',
            'scope'         => null,
            'state'         => $state,
        ]);

        return redirect(config('custom.oauth_url') . '/oauth/authorize?' . $query);
    }

    /**
     * Traite le retour du serveur Passport : échange le code, récupère
     * l'identité de l'utilisateur, crée la session BFF (Sanctum) et
     * persiste les tokens liés à CETTE session précise du navigateur.
     */
    public function handleCallback(Request $request)
    {
        $state = $request->session()->pull('state');

        throw_unless(
            strlen((string) $state) > 0 && $state === $request->state,
            InvalidOauthStateException::class
        );

        $payload = [
            'grant_type'    => 'authorization_code',
            'client_id'     => config('custom.oauth.client_id'),
            'client_secret' => config('custom.oauth.client_secret'),
            'redirect_uri'  => config('custom.oauth.redirect_uri'),
            'code'          => $request->code,
        ];

        $response = Http::asForm()->post(config('custom.oauth_url') . '/oauth/token', $payload);

        if ($response->failed()) {
            abort(401, "Impossible d'obtenir le token OAuth.");
        }

        $tokens = $response->json();

        if (! isset($tokens['access_token'], $tokens['refresh_token'], $tokens['expires_in'])) {
            abort(401, 'Réponse OAuth invalide.');
        }

        $userInfo = Http::withToken($tokens['access_token'])
            ->get(config('custom.oauth_url') . '/api/user');

        if ($userInfo->failed()) {
            abort(401, 'Token invalide.');
        }

        $userInfo = $userInfo->json();

        $user = User::firstOrCreate(
            ['oauth_provider_id' => $userInfo['sub']],
            [
                'sub'       => Str::uuid()->toString(),
                'firstname' => $userInfo['firstname'],
                'lastname'  => $userInfo['lastname'],
                'email'     => $userInfo['email'],
            ]
        );

        // firstOrCreate n'exécute le update que si l'utilisateur existait déjà ;
        // s'il vient d'être créé, les valeurs ci-dessus sont déjà les bonnes.
        $user->wasRecentlyCreated || $user->update([
            'firstname' => $userInfo['firstname'],
            'lastname'  => $userInfo['lastname'],
            'email'     => $userInfo['email'],
        ]);

        // On connecte d'abord l'utilisateur et on régénère la session
        // AVANT de persister les tokens, pour que la clé de session
        // stockée soit bien celle de la session finale (anti session-fixation).
        Auth::login($user);
        $request->session()->regenerate();

        OauthSession::updateOrCreate(
            [
                'user_id'    => $user->id,
                'session_id' => $request->session()->getId(),
            ],
            [
                'access_token'     => $tokens['access_token'],
                'refresh_token'    => $tokens['refresh_token'],
                'expires_at'       => now()->addSeconds($tokens['expires_in']),
                'last_activity_at' => now(),
            ]
        );

        return redirect(config('custom.frontend_url'));
    }

    /**
     * Déconnecte l'utilisateur du BFF ET révoque le token côté serveur Passport,
     * pour ne pas laisser un refresh_token valide traîner en base après logout.
     */
    public function logout(Request $request)
    {

    
        $user = $request->user();

        if ($user) {
            $session = OauthSession::where('user_id', $user->id)
                ->where('session_id', $request->session()->getId())
                ->first();

            if ($session) {
                // Adaptez l'URL si votre serveur Passport expose un endpoint
                // de révocation différent (ex: /oauth/token/revoke custom).
                Http::asForm()->post(config('custom.oauth_url') . '/oauth/token/revoke', [
                    'client_id'     => config('custom.oauth.client_id'),
                    'client_secret' => config('custom.oauth.client_secret'),
                    'token'         => $session->refresh_token,
                ]);

                $session->delete();
            }
        }

        Auth::guard('web')->logout(); // <-- guard explicite

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();

        // return redirect(config('custom.frontend_url'));
    }
}