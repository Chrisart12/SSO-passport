<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OauthSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'access_token',
        'refresh_token',
        'expires_at',
        'last_activity_at',
    ];

    protected $casts = [
        // Chiffrement au repos : jamais de tokens en clair en base
        'access_token'     => 'encrypted',
        'refresh_token'    => 'encrypted',
        'expires_at'       => 'datetime',
        'last_activity_at' => 'datetime',
    ];

    /**
     * Marge de sécurité pour éviter d'utiliser un token sur le point d'expirer
     * (évite un refresh "juste après" échoué côté serveur distant).
     */
    protected const EXPIRY_LEEWAY_SECONDS = 30;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at
            ->subSeconds(self::EXPIRY_LEEWAY_SECONDS)
            ->isPast();
    }
}