<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Partage extends Model
{
    use HasFactory;

    protected $table = 'partages';

    /**
     * Attributs remplissables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bouteille_id',
        'token_unique',
        'expires_at',
    ];

    /**
     * Casts pour certains types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Un partage appartient à une bouteille.
     */
    public function bouteille(): BelongsTo
    {
        return $this->belongsTo(Bouteille::class, 'bouteille_id');
    }

    /**
     * Génère un token unique pour le partage.
     *
     * @return string
     */
    public static function generateToken(): string
    {
        return Str::random(32);
    }

    /**
     * Vérifie si le partage est expiré.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false; // Pas d'expiration définie
        }

        return $this->expires_at->isPast();
    }
}
