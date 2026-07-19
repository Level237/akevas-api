<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Town;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TownController extends Controller
{
    public function index()
    {
        // 🚨 CACHE DE 30 JOURS : Les villes ne changent presque jamais.
        // On ne sélectionne que les colonnes nécessaires pour alléger le JSON.
        $towns = Cache::remember('towns.all', now()->addDays(30), function () {
            return Town::select('id', 'town_name')->get();
        });

        return response()->json($towns);

    }

    public function store(Request $request)
    {
        $town = Town::create($request->all());

        // 🚨 INVALIDATION : On vide le cache pour que la nouvelle ville apparaisse immédiatement
        Cache::forget('towns.all');

        return response()->json($town, 201);
    }

    public function show(string $id)
    {
        // Pas besoin de cache ici, c'est pour l'admin, très peu utilisé
        $town = Town::find($id);
        return response()->json($town, 200);
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $town = Town::findOrFail($id);
            $town->town_name = $request->town_name;
            $town->save();
            DB::commit();

            // 🚨 INVALIDATION
            Cache::forget('towns.all');

            return response()->json([
                'success' => true,
                'message' => 'Ville mise à jour avec succès',
                'data' => $town
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la ville',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        // 🚨 CORRECTION DE BUG : Utilisation de withCount et correction de la syntaxe
        $town = Town::withCount('shops')->find($id);

        if (!$town) {
            return response()->json(['message' => 'Ville introuvable'], 404);
        }

        // Correction : $town->shops_count (et non $town::shops_count)
        if ($town->shops_count > 0) {
            return response()->json([
                'message' => "Impossible de supprimer : cette ville est liée à {$town->shops_count} boutique(s)."
            ], 422);
        }

        $town->delete();

        // 🚨 INVALIDATION
        Cache::forget('towns.all');

        return response()->json(['message' => 'Ville supprimée avec succès'], 200);
    }
}