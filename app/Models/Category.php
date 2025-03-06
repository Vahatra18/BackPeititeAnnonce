<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table  = 'categories';
    protected $primaryKey = 'id_category';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'nom',
        'description',
        'parent_id',
    ];

    //relation avec sous category
    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id_category');
    }

    //relation avec category parent 
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id_category');
    }
}
