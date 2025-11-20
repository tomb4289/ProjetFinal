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

    // Désactive les colonnes 'created_at' et 'updated_at' 
    public $timestamps = false; 


    // Colonnes remplissables
    protected $fillable = [
        'nom',
        'user_id',
    ];

    /**
     * Le cellier appartient à un utilisateur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

   

    /**
     * Le cellier contient plusieurs bouteilles.
     */
    public function bouteilles(): HasMany
    {
        return $this->hasMany(Bouteille::class, 'cellier_id');
    }
}
