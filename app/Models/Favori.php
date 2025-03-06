<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favori extends Model
{
    protected $primaryKey = 'id_favoris'; // Clé primaire personnalisée
    protected $fillable = [
        'id_utilisateur',
        'id_ad'
    ];

    // Relationship : A favorite belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class, 'id_utilisateur', 'id');
    }

    // Relationship : A favorite belongs to an ad
    public function ad()
    {
        return $this->belongsTo(Ad::class, 'id_ad', 'id_ad');
    }

    use HasFactory;
}
