<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Ad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class MessageController extends Controller
{
    /**
     * Display a listing of the messages for a specific user or ad.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $adId = $request->query('ad_id');

        if ($adId) {
            $messages = Message::where('id_ad', $adId)
                ->with(['sender', 'receiver', 'ad'])
                ->get();
        } else {
            $messages = Message::where('id_send', $userId)
                ->orWhere('id_rec', $userId)
                ->with(['sender', 'receiver', 'ad'])
                ->get();
        }

        return response()->json(['data' => $messages], 200);
    }

    /**
     * Store a newly created message in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_send' => 'required|exists:users,id',
                'id_rec' => 'required|exists:users,id',
                'id_ad' => 'required|exists:ads,id_ad',
                'contenu' => 'required|string',
                'est_lu' => 'boolean', // Optionnel, par défaut 0
            ]);

            if (!Auth::check()) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }

            $userId = Auth::id();
            if ($userId !== (int)$request->id_expéditeur) {
                return response()->json(['error' => 'Non autorisé : Tu ne peux envoyer un message qu’en ton nom'], 403);
            }

            $ad = Ad::findOrFail($request->id_ad);
            if ($ad->id_utilisateur === $userId) {
                return response()->json(['error' => 'Non autorisé : Tu ne peux pas t’envoyer un message à toi-même pour ton annonce'], 403);
            }

            $message = Message::create([
                'id_send' => $request->id_expéditeur,
                'id_rec' => $request->id_récepteur,
                'id_ad' => $request->id_ad,
                'contenu' => $request->contenu,
                'est_lu' => $request->boolean('est_lu', false), // Défaut à 0 si non spécifié
            ]);

            return response()->json(['data' => $message, 'message' => 'Message envoyé avec succès'], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Erreur de validation', 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de l’envoi du message', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id_message)
    {
        $message = Message::with(['sender', 'receiver', 'ad'])->findOrFail($id_message);
        $userId = Auth::id();

        if ($userId !== $message->id_expéditeur && $userId !== $message->id_récepteur) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        // Marque le message comme lu si l’utilisateur est le destinataire
        if ($userId === $message->id_récepteur && !$message->est_lu) {
            $message->update(['est_lu' => 1]);
        }

        return response()->json(['data' => $message], 200);
    }

    /**
     * Update the specified resource in storage (par exemple, marquer comme lu ou modifier le contenu).
     */
    public function update(Request $request, $id_message)
    {
        try {
            $request->validate([
                'contenu' => 'string', // Optionnel, pour modifier le contenu
                'est_lu' => 'boolean', // Optionnel, pour marquer comme lu/non lu
            ]);

            if (!Auth::check()) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }

            $message = Message::findOrFail($id_message);
            $userId = Auth::id();

            if ($userId !== $message->id_expéditeur && $userId !== $message->id_récepteur) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            // Seuls les expéditeurs peuvent modifier le contenu
            if ($request->has('contenu') && $userId !== $message->id_expéditeur) {
                return response()->json(['error' => 'Non autorisé : Seuls les expéditeurs peuvent modifier le contenu'], 403);
            }

            // Les destinataires et expéditeurs peuvent marquer comme lu/non lu
            if ($request->has('est_lu')) {
                $message->update(['est_lu' => $request->boolean('est_lu')]);
            }

            if ($request->has('contenu')) {
                $message->update(['contenu' => $request->contenu]);
            }

            return response()->json(['data' => $message, 'message' => 'Message mis à jour avec succès'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Erreur de validation', 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la mise à jour du message', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id_message)
    {
        try {
            $message = Message::findOrFail($id_message);
            $userId = Auth::id();

            if ($userId !== $message->id_expéditeur && $userId !== $message->id_récepteur) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            $message->delete();
            return response()->json(['message' => 'Message supprimé avec succès'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression du message', 'message' => $e->getMessage()], 500);
        }
    }
}
