<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bouteille extends Model
{
    use HasFactory;

    protected $table = 'bouteilles';

    /**
     * Attributs remplissables en masse.
     *
     * On inclut ici tous les champs utilisés dans tes contrôleurs
     * + ceux ajoutés par les migrations récentes (type, millesime, code_saq, etc.).
     */
    protected $fillable = [
        'cellier_id',
        'nom',
        'pays',
        'type',
        'millesime',
        'format',
        'quantite',
        'prix',
        'commentaire',
        'code_saq',
        'note_degustation',
        'rating',
    ];

    /**
     * Casts pour certains types.
     */
    protected $casts = [
        'prix'       => 'decimal:2',
        'quantite'   => 'integer',
        'millesime'  => 'integer',
        'rating'     => 'integer',
    ];

    /**
     * Une bouteille appartient à un cellier.
     */
    public function cellier(): BelongsTo
    {
        return $this->belongsTo(Cellier::class, 'cellier_id');
    }

    /**
     * Une bouteille peut avoir plusieurs partages.
     */
    public function partages(): HasMany
    {
        return $this->hasMany(Partage::class, 'bouteille_id');
    }

    /**
     * Récupère l'image de la bouteille depuis le catalogue SAQ si elle existe.
     * 
     * @return string|null URL de l'image ou null si non trouvée
     */
    public function getImageFromCatalogue(): ?string
    {
        if (!$this->code_saq) {
            return null;
        }

        $bouteilleCatalogue = BouteilleCatalogue::where('code_saQ', $this->code_saq)->first();
        
        return $bouteilleCatalogue?->image;
    }
}
