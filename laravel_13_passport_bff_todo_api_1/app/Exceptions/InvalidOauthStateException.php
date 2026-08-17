<?php

namespace App\Exceptions;

use Exception;

class InvalidOauthStateException extends Exception
{
    protected $message = 'Le paramètre state est invalide ou manquant.';
}