<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Town;
use Illuminate\Http\Request;

class TownController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $towns = Town::all();
        return response()->json(['towns' => $towns], 200);
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
    public function update(Request $request, string $id)
    {
        $town = Town::find($id);
        $town->update($request->all());
        return response()->json($town, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $town = Town::find($id);
        $town->delete();
        return response()->json($town, 200);
    }
}
