<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Rules\ValidCity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        $ads = Ad::with('user', 'category', 'images')->get();
        return response()->json(['data' => $ads], 200);
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_utilisateur' => 'required|exists:users,id',
                'id_category' => 'required|exists:categories,id_category',
                'titre' => 'required|string|max:100',
                'description' => 'required|string',
                'prix' => 'nullable|numeric|min:0|max:99999999.99', // DECIMAL(10,2), optionnel, positif
                'emplacement' => ['required', 'string', 'max:100', new ValidCity], // VARCHAR(100), requis
                'statut' => 'required|in:en attente,actif,expiré,supprimé', // ENUM, requis avec valeurs spécifiques
            ]);

            if (!Auth::check()) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }

            if (Auth::id() !== (int)$request->id_utilisateur) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            $ad = Ad::create($request->all());

            return response()->json(['data' => $ad, 'message' => 'Annonce créée avec succès'], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Erreur de validation', 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la création de l’annonce', 'message' => $e->getMessage()], 500);
        }
    }

    /*
     * Display the specified resource.
     */
    public function show($id_ad)
    {
        $ad = Ad::with('user', 'category', 'images')->findOrFail($id_ad);
        return response()->json(['data' => $ad], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_ad)
    {
        try {
            $request->validate([
                'id_utilisateur' => 'required|exists:users,id',
                'id_category' => 'required|exists:categories,id_category',
                'titre' => 'required|string|max:100',
                'description' => 'required|string',
                'prix' => 'nullable|numeric|min:0|max:99999999.99', // DECIMAL(10,2), optionnel, positif
                'emplacement' => ['required', 'string', 'max:100', new ValidCity], // VARCHAR(100), requis
                'statut' => 'required|in:en attente,actif,expiré,supprimé', // ENUM, requis avec valeurs spécifiques
            ]);

            if (!Auth::check()) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }

            if (Auth::id() !== (int)$request->id_utilisateur) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            $ad = Ad::findOrFail($id_ad);
            $ad->update($request->all());

            return response()->json(['data' => $ad, 'message' => 'Annonce mise à jour avec succès'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Erreur de validation', 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la mise à jour de l’annonce', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_ad)
    {
        try {
            $ad = Ad::findOrFail($id_ad);

            if (!Auth::check() || Auth::id() !== $ad->id_utilisateur) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            $ad->delete();
            return response()->json(['message' => 'Annonce supprimée avec succès'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression de l’annonce', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Filtrer les annonces par nom de catégorie.
     */
    public function filterByCategoryName(Request $request)
    {
        try {
            // Valider le paramètre de requête "category_name"
            $request->validate([
                'category_name' => 'required|string|max:100',
            ]);

            // Récupérer le nom de la catégorie depuis la requête
            $categoryName = $request->input('category_name');

            // Filtrer les annonces par nom de catégorie
            $ads = Ad::whereHas('category', function ($query) use ($categoryName) {
                $query->where('nom', 'like', '%' . $categoryName . '%');
            })->with('user', 'category', 'images')->get();

            // Retourner les annonces filtrées
            return response()->json(['data' => $ads], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Erreur de validation', 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du filtrage des annonces', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Filtrer les annonces par ville.
     */
    public function filterByCity(Request $request)
    {
        try {
            // Valider le paramètre de requête "city"
            $request->validate([
                'city' => 'required|string|max:100',
            ]);

            // Récupérer le nom de la ville depuis la requête
            $city = $request->input('city');

            // Filtrer les annonces par ville
            $ads = Ad::where('emplacement', 'like', '%' . $city . '%')
                ->with('user', 'category', 'images')
                ->get();

            // Retourner les annonces filtrées
            return response()->json(['data' => $ads], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Erreur de validation', 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du filtrage des annonces', 'message' => $e->getMessage()], 500);
        }
    }
}
