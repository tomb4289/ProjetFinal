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
     * Un cellier appartient à un utilisateur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Un cellier contient plusieurs bouteilles.
     */
    public function bouteilles(): HasMany
    {
        return $this->hasMany(Bouteille::class, 'cellier_id');
    }

    /**
     * Retourne les bouteilles de ce cellier triées selon une colonne et un sens.
     * 
     * La validation du nom de colonne et de la direction
     * doit être faite dans le contrôleur
     *
     * @param string $sortColumn Colonne de tri (ex: 'nom', 'pays', 'quantite', 'prix', etc.)
     * @param string $direction  'asc' ou 'desc'
     * @return \Illuminate\Support\Collection
     */
    public function getBouteillesTriees(string $sortColumn = 'nom', string $direction = 'asc')
    {
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        return $this->bouteilles()
            ->orderBy($sortColumn, $direction)
            ->get();
    }

    /**
     * Recherche des bouteilles dans ce cellier avec tri.
     *
     * @param string|null $term      Terme de recherche (nom, pays, type, format…)
     * @param string      $sortColumn Colonne de tri
     * @param string      $direction  'asc' ou 'desc'
     * @return \Illuminate\Support\Collection
     */
    public function searchBouteilles(?string $term, string $sortColumn = 'nom', string $direction = 'asc')
    {
        $query = $this->bouteilles();

        if (!empty($term)) {
            $term = '%'.$term.'%';

            $query->where(function ($q) use ($term) {
                $q->where('nom', 'LIKE', $term)
                  ->orWhere('pays', 'LIKE', $term)
                  ->orWhere('type', 'LIKE', $term)
                  ->orWhere('format', 'LIKE', $term);
            });
        }

        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        return $query
            ->orderBy($sortColumn, $direction)
            ->get();
    }
}
