<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Modèle représentant un pays
class Pays extends Model
{
    use HasFactory;

    protected $table = 'pays';

    public $timestamps = false;

    // Champs pouvant être assignés en masse
    protected $fillable = [
        'nom',
        'date_creation',
    ];

    // Définition des types de données pour certains attributs
    protected function casts(): array
    {
        return [
            'date_creation' => 'datetime',
        ];
    }

    // Relation Eloquent : un pays peut avoir plusieurs bouteilles dans le catalogue
    public function bouteillesCatalogue()
    {
        return $this->hasMany(BouteilleCatalogue::class, 'id_pays');
    }
}

