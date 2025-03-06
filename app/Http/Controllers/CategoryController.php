<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Ajouter une catégorie principale
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
            'message' => 'Catégorie ajoutée avec succès',
            'data' => $categorie,
        ], 201);
    }

    // Ajouter une sous-catégorie
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
            'message' => 'Sous-catégorie ajoutée avec succès',
            'subcategory' => $sous_Category,
        ], 201);
    }


    /**
     * Afficher toutes les catégories parentes (sans sous-catégories).
     */
    public function afficherCategoriesParentes()
    {
        // Récupérer toutes les catégories parentes (où parent_id est null)
        $categoriesParentes = Category::whereNull('parent_id')->get();

        return response()->json([
            'categories_parentes' => $categoriesParentes,
        ]);
    }

    /**
     * Afficher toutes les sous-catégories d'une catégorie parente.
     */
    public function afficherSousCategories($idParent)
    {
        // Récupérer la catégorie parente
        $categorieParente = Category::find($idParent);

        if (!$categorieParente) {
            return response()->json([
                'message' => 'Catégorie parente non trouvée',
            ], 404);
        }

        // Récupérer toutes les sous-catégories de cette catégorie parente
        $sousCategories = Category::where('parent_id', $idParent)->get();

        return response()->json([
            'categorie_parente' => $categorieParente,
            'sous_categories' => $sousCategories,
        ]);
    }

    // Afficher toutes les catégories avec leurs sous-catégories
    public function affichagecategory()
    {
        $categories = Category::with('subcategories')->whereNull('parent_id')->get();

        return response()->json([
            'categories' => $categories,
        ]);
    }

    // Afficher une catégorie par son ID
    public function show($id)
    {
        $category = Category::with('subcategories')->find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Catégorie non trouvée',
            ], 404);
        }

        return response()->json([
            'category' => $category,
        ]);
    }

    // Modifier une catégorie
    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'sometimes|string|max:50|unique:categories,nom,' . $id . ',id_category',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id_category',
        ]);

        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Catégorie non trouvée',
            ], 404);
        }

        $category->update([
            'nom' => $request->nom ?? $category->nom,
            'description' => $request->description ?? $category->description,
            'parent_id' => $request->parent_id ?? $category->parent_id,
        ]);

        return response()->json([
            'message' => 'Catégorie mise à jour avec succès',
            'data' => $category,
        ]);
    }

    // Supprimer une catégorie
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Catégorie non trouvée',
            ], 404);
        }

        // Supprimer également les sous-catégories associées
        $category->subcategories()->delete();
        $category->delete();

        return response()->json([
            'message' => 'Catégorie et ses sous-catégories supprimées avec succès',
        ]);
    }
}
