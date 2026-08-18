<?php

namespace App\Exceptions;

use Exception;

class ExpiredOauthSessionException extends Exception
{
    protected $message = 'La session OAuth a expiré et ne peut plus être rafraîchie. Reconnexion requise.';
}