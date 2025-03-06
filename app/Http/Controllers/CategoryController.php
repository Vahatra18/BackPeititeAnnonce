<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //categorie principale
    public function storecategory(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:50|unique:categories,nom',
            'description' => 'nullable|string'
        ]);

        $categorie = Category::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'parent_id' => null,
        ]);

        return response()->json([
            'message' => 'categorie ajouter avec succes',
            'data' => $categorie,
        ], 201);
    }


    //ajout sous category

    public function createsouscategory(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:50|unique:categories,nom',
            'description' => 'nullable|string',
            'parent_id' => 'required|exists:categories,id_category',
        ]);

        $sous_Category = Category::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'parent_id' => $request->parent_id,
        ]);
        return response()->json([
            'message' => 'sous categorie ajouter avec success',
            'subcategory' => $sous_Category,
        ], 201);
    }

    //liste de tout les categories avec les sous categorie

    public function affichagecategory()
    {
        $categories = Category::with('subcategories')->whereNull('parent_id')->get();

        return response()->json([
            'categories' => $categories,
        ]);
    }
}
