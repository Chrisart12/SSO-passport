<?php

namespace App\Providers;

use App\Models\Passport\Client;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Contracts\AuthorizationViewResponse;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthorizationViewResponse::class, function () {
            return new class implements AuthorizationViewResponse {
                private array $parameters = [];

                // ← valeur par défaut [] obligatoire pour matcher l'interface
                public function withParameters(array $parameters = []): static
                {
                    $this->parameters = $parameters;
                    return $this;
                }

                public function toResponse($request)
                {
                    return response()->view(
                        'vendor.passport.authorize',
                        $this->parameters
                    );
                }
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Permet d'utiliser notre modèle Client personnalisé pour Passport
        // Afin de surcharger la méthode skipsAuthorization et permettre à tous les clients de passer l'autorisation sans demander la permission à l'utilisateur.
        // Pour un client  de confiance BFF backend-for-frontend, on peut utiliser cette approche pour éviter de demander à l'utilisateur de confirmer l'autorisation.
        Passport::useClientModel(Client::class);
    }
}
