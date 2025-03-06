<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $primaryKey = 'id_message'; // Clé primaire personnalisée
    protected $fillable = [
        'id_send',
        'id_rec',
        'id_ad',
        'contenu',
        'est_lu'
    ];

    // Relationship : A message belongs to an sender (user)
    public function sender()
    {
        return $this->belongsTo(User::class, 'id_send', 'id');
    }

    // Relationship : A message belongs to a receiver (user)
    public function receiver()
    {
        return $this->belongsTo(User::class, 'id_rec', 'id');
    }

    // Relationship : A message belongs to an ad
    public function ad()
    {
        return $this->belongsTo(Ad::class, 'id_ad', 'id_ad');
    }

    use HasFactory;
}