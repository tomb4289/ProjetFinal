<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cellier extends Model
{
    use HasFactory;

    // Table associée
    protected $table = 'celliers';

    // Désactive les colonnes 'created_at' et 'updated_at' (selon le schéma bd_vino.pdf)
    public $timestamps = false; 

    // ✔ Garde les timestamps créés par Laravel
    //public $timestamps = true;

    // Colonnes remplissables
    protected $fillable = [
        'nom',
        'id_utilisateur',
    ];

    /**
     * Le cellier appartient à un utilisateur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias en français si jamais on l’utilise dans des vues.
     */
    public function utilisateur(): BelongsTo
    {
        return $this->user();
    }

    /**
     * Le cellier contient plusieurs bouteilles.
     */
    public function bouteilles(): HasMany
    {
        return $this->hasMany(Bouteille::class, 'cellier_id');
    }
}
