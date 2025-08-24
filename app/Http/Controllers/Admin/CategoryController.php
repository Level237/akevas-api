<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $categories = Category::whereDoesntHave('parent')->get();
        return response()->json(['categories'=>$categories],200);
    }

    public function all(){
        $categories = CategoryResource::collection(Category::all());
        return response()->json(['categories'=>$categories],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
            $category = new Category;
            $category->category_name = $request->category_name;
            $category->category_url = \Illuminate\Support\Str::slug($request->category_name);
            
            if($request->category_profile){
                $file=$request->file('category_profile');
                $category->category_profile = $file->store('categories/profile', 'public');
            }
            
            if($request->parent_id){
                $category->parent_id = $request->parent_id;
            }
            
            $category->save();
            
            // Attacher les genres si fournis
            if($request->gender_id){
                $category->genders()->attach($request->gender_id);
            }
            
            return response()->json([
                'success' => true,
                'message' => "Category created successfully",
                'data' => $category->load('genders')
            ], 200);
            
        } catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
