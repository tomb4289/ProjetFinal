<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Modèle représentant une bouteille dans le catalogue
class BouteilleCatalogue extends Model
{
    use HasFactory;

    protected $table = 'bouteille_catalogue';

    // Champs pouvant être assignés en masse
    protected $fillable = [
        'code_saQ',
        'nom',
        'id_type_vin',
        'id_pays',
        'id_region',
        'millesime',
        'prix',
        'url_image',
        'volume',
        'date_import',
    ];

    // Définition des types de données pour certains attributs
    protected function casts(): array
    {
        return [
            'prix' => 'decimal:2',
            'millesime' => 'integer',
            'date_import' => 'datetime',
        ];
    }

    /**
     * Accessor pour obtenir l'URL complète de l'image formatée.
     * Permet d'utiliser $bouteille->image au lieu de $bouteille->url_image
     * avec la normalisation du chemin.
     */
    public function getImageAttribute()
    {
        if (!$this->url_image) {
            return null;
        }

        // Normaliser le chemin : enlever tous les préfixes storage/ et / au début
        $imagePath = ltrim($this->url_image, '/');
        
        // Enlever tous les préfixes "storage/" jusqu'à ce qu'il n'y en ait plus
        while (str_starts_with($imagePath, 'storage/')) {
            $imagePath = substr($imagePath, 8); // Enlever "storage/" (8 caractères)
        }
        
        // Ajouter storage/ une seule fois et utiliser asset() pour générer l'URL complète
        return asset('storage/' . $imagePath);
    }

    // Relations Eloquent
    public function pays()
    {
        return $this->belongsTo(Pays::class, 'id_pays');
    }

    public function typeVin()
    {
        return $this->belongsTo(TypeVin::class, 'id_type_vin');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'id_region');
    }
}
