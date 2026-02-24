<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Town;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class TownController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $towns = Town::all();
        return response()->json($towns);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $town = Town::create($request->all());
        return response()->json($town, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $town = Town::find($id);
        return response()->json($town, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {


        try {
            DB::beginTransaction();

            $town = Town::findOrFail($id);


            $town->town_name = $request->town_name;




            $town->save();




            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ville mise à jour avec succès',
                'data' => $town
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la ville',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $town = Town::withCount('shops')->find($id);

        if (!$town) {
            return response()->json(['message' => 'Ville introuvable'], 404);
        }

        // On vérifie si le compteur de relations est supérieur à 0
        if ($town::shops_count > 0) {
            return response()->json([
                'message' => "Impossible de supprimer : cette ville est liée à {$town->shops_count} boutique(s)."
            ], 422);
        }

        $town->delete();
        return response()->json(['message' => 'Ville supprimée avec succès'], 200);
    }
}
