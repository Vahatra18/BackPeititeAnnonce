<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Image;
//use Google\Service\ServiceControl\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Display a listing of the images for a spefic ad.
     */
    public function index($id_ad)
    {
        $ad = Ad::findOrFail($id_ad);
        $images = $ad->images()->get();
        return response()->json(['data' => $images], 200);
    }

    /**
     * Store a newly created image in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_ad' => 'required|exists:ads,id_ad',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', //Validation for an image file(max 2MB)
                'est_principal' => 'boolean', // Optional, BOOLEAN
            ]);

            if (!Auth::check()) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }

            $ad = Ad::findOrFail($request->id_ad);
            if (Auth::id() !== $ad->id_utilisateur) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            // Store the image in /storage/app/public/images
            $path = $request->file('image')->store('images', 'public');

            //Check if there is already main image for this ad  
            if ($request->has('est_principal') && $request->est_principal) {
                $ad->images()->update(['est_principal' => 0]); //Disable other main images 
            }

            $image = Image::create([
                'id_ad' => $request->id_ad,
                'path' => $path, //Store relative path(ex. : "images/annonce_1.jpg")
                'est_principal' => $request->boolean('est_principal', false), // Défault  0 if not custom
            ]);

            return response()->json(['data' => $image, 'message' => 'Image uploadée avec succès'], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Erreur de validation', 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de l’upload de l’image', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id_image)
    {
        $image = Image::findOrFail($id_image);
        $ad = $image->ad;
        if (!Auth::check() || Auth::id() !== $ad->id_utilisateur) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }
        return response()->json(['data' => $image], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id_image)
    {
        try {
            $request->validate([
                'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Optionnel, pour un nouveau upload
                'est_principal' => 'boolean',
            ]);

            if (!Auth::check()) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }

            $image = Image::findOrFail($id_image);
            $ad = $image->ad;
            if (Auth::id() !== $ad->id_utilisateur) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            // Si une nouvelle image est uploadée, supprime l’ancienne et stocke la nouvelle
            if ($request->hasFile('image')) {
                // Supprime l’ancienne image si elle existe
                if ($image->path && Storage::disk('public')->exists($image->path)) {
                    Storage::disk('public')->delete($image->path);
                }
                // Stocke la nouvelle image
                $path = $request->file('image')->store('images', 'public');
                $image->path = $path;
            }

            // Vérifie s’il y a un changement d’image principale
            if ($request->has('est_principal') && $request->est_principal) {
                $ad->images()->update(['est_principal' => 0]); // Désactive les autres images principales
            }

            $image->update([
                'path' => $image->path, // Garde le chemin actuel ou met à jour avec le nouveau
                'est_principal' => $request->boolean('est_principal', $image->est_principal),
            ]);

            return response()->json(['data' => $image, 'message' => 'Image mise à jour avec succès'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Erreur de validation', 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la mise à jour de l’image', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_image)
    {
        try {
            $image = Image::findOrFail($id_image);
            $ad = $image->ad;

            if (!Auth::check() || Auth::id() !== $ad->id_utilisateur) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            // Supprime le fichier physique si existant
            if ($image->path && Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }

            $image->delete();
            return response()->json(['message' => 'Image supprimée avec succès'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression de l’image', 'message' => $e->getMessage()], 500);
        }
    }
}
