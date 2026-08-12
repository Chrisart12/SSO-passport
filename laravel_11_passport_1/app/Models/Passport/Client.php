<?php

namespace App\Models\Passport;

use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Passport\Client as BaseClient;

class Client extends BaseClient
{
    public function skipsAuthorization(Authenticatable $user, array $scopes): bool
    {
        // return $this->firstParty();
        // return in_array($this->getKey(), config('custom.trusted_client_ids', []));
        return in_array($this->getKey(), config('custom.trusted_client_ids', []));
    }
}