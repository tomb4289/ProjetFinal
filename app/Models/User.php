<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany; 
use App\Models\Cellier;
use App\Models\ListeAchat;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
        ];
    }

    /**
     * Un user possède plusieurs celliers.
     */
    public function celliers(): HasMany
    {
        return $this->hasMany(Cellier::class, 'user_id', 'id');
    }

    /**
     * Liste d'achat liée à l'utilisateur.
     */
    public function listeAchat(): HasMany
    {
        return $this->hasMany(ListeAchat::class, 'user_id', 'id');
    }


    /**
     * Vérifier le rôle admin.
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }
}
