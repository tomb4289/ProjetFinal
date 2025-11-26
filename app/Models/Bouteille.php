<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bouteille extends Model
{
    use HasFactory;

    /**
     * Attributs remplissables en création/mise à jour.
     */
    protected $fillable = [
        'cellier_id',
        'nom',
        'pays',
        'type',
        'format',
        'quantite',
        'prix',          
        'code_saq',
        'millesime',
        'note_degustation',
        'rating',
    ];
    /**
     * Ici on force le prix à être un décimal avec 2 chiffres
     */
    protected function casts(): array
    {
        return [
            'prix' => 'decimal:2',
            'rating' => 'integer',
        ];
    }

    /**
     * Relation : cette bouteille appartient à un cellier.
     */
    public function cellier()
    {
        return $this->belongsTo(Cellier::class);
    }

    public function addToCellier(Cellier $cellier, array $attributes) {}
}
