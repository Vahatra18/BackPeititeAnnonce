<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $primaryKey = 'id_ad'; //Custom primary key 
    protected $fillable = [
        'id_utilisateur',
        'id_category',
        'titre',
        'telephone',
        'description',
        'prix',
        'emplacement',
        'statut'
    ];

    //Relationship : An ad belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class, 'id_utilisateur', 'id');
    }

    //Relationship : An ad belongs to a category
    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category', 'id_category');
    }

    //Relationship : An ad has many images
    public function images()
    {
        return $this->hasMany(Image::class, 'id_ad', 'id_ad');
    }
    use HasFactory;
}
