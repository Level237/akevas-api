<?php

namespace App\Http\Controllers\Admin;

use App\Models\Quarter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\QuarterResource;

class QuarterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $quarters = Quarter::all();
        return response()->json(['quarters' => QuarterResource::collection($quarters)]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $quarter = Quarter::create($request->all());
        return response()->json($quarter, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $quarter = Quarter::find($id);
        return response()->json($quarter, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $quarter = Quarter::find($id);
        $quarter->update($request->all());
        return response()->json($quarter, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $quarter = Quarter::find($id);
        $quarter->delete();
        return response()->json($quarter, 200);
    }
}
