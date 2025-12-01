<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListeAchat extends Model
{
    protected $table = 'liste_achat';
    protected $fillable = [
        'user_id',
        'bouteille_catalogue_id',
        'quantite',
        'achete',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bouteilleCatalogue()
    {
        return $this->belongsTo(BouteilleCatalogue::class);
    }
}
