<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $primaryKey = 'id_image'; //custom primary key
    protected $fillable = [
        'id_ad',
        'path',
        'est_principal'
    ];

    // Relationship : An image belongs to an Ad
    public function ad()
    {
        return $this->belongsTo(Ad::class, 'id_ad', 'id_ad');
    }
    use HasFactory;
}
