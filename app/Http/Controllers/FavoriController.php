<?php

namespace App\Http\Controllers;

use App\Models\Favori;
use App\Models\Ad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FavoriController extends Controller
{
    /**
     * Display a listing of the favorites for the authenticated user.
     */
    public function index()
    {
        $userId = Auth::id();
        $favoris = Favori::where('id_utilisateur', $userId)
            ->with(['ad', 'user'])
            ->get();

        return response()->json(['data' => $favoris], 200);
    }

    /**
     * Store a newly created favorite in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_ad' => 'required|exists:ads,id_ad',
            ]);

            if (!Auth::check()) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }

            $userId = Auth::id();
            $adId = $request->id_ad;

            // Vérifie si l’utilisateur a déjà favori cette annonce
            $existingFavori = Favori::where('id_utilisateur', $userId)
                ->where('id_ad', $adId)
                ->first();

            if ($existingFavori) {
                return response()->json(['error' => 'Cette annonce est déjà dans vos favoris'], 400);
            }

            $favori = Favori::create([
                'id_utilisateur' => $userId,
                'id_ad' => $adId,
            ]);

            return response()->json(['data' => $favori, 'message' => 'Annonce ajoutée aux favoris avec succès'], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Erreur de validation', 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de l’ajout aux favoris', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id_favoris)
    {
        $favori = Favori::with(['ad', 'user'])->findOrFail($id_favoris);
        $userId = Auth::id();

        if ($userId !== $favori->id_utilisateur) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        return response()->json(['data' => $favori], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_favoris)
    {
        try {
            $favori = Favori::findOrFail($id_favoris);
            $userId = Auth::id();

            if ($userId !== $favori->id_utilisateur) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            $favori->delete();
            return response()->json(['message' => 'Annonce retirée des favoris avec succès'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression des favoris', 'message' => $e->getMessage()], 500);
        }
    }
}
