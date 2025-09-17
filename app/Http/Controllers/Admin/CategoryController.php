<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use Illuminate\Support\Facades\Storage;
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
        $categories = CategoryResource::collection(Category::orderBy('created_at', 'desc')->get());
        return response()->json(['categories'=>$categories],200);
    }

    public function getCategory($id){
        $category = Category::findOrFail($id);
        return response()->json(['category'=>CategoryResource::make($category)],200);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'gender_id' => 'required|integer|exists:genders,id',
            'parent_id' => 'nullable|integer|exists:categories,id',
            'category_profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $category = Category::findOrFail($id);

            // Mettre à jour les données de base
            $category->category_name = $request->category_name;
            $category->parent_id = $request->parent_id;

            // Gérer l'image de profil
            if ($request->hasFile('category_profile')) {
                // Supprimer l'ancienne image si elle existe
                if ($category->category_profile) {
                    Storage::disk('public')->delete($category->category_profile);
                }

                // Stocker la nouvelle image
                $image = $request->file('category_profile');
                $imageName = time() . '_' . Str::slug($request->category_name) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->store('categories/profile', $imageName, 'public');
                $category->category_profile = $imagePath;
            }

            // Générer l'URL de la catégorie si elle n'existe pas
            if (!$category->category_url) {
                $category->category_url = Str::slug($request->category_name);
            }

            $category->save();

            // Mettre à jour la relation avec le genre
            $category->genders()->sync([$request->gender_id]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Catégorie mise à jour avec succès',
                'data' => $category->load('genders', 'parent')
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la catégorie',
                'error' => $e->getMessage()
            ], 500);
        }
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
    

    /**
     * Remove the specified resource from storage.
     * 
     * This method safely deletes a category by checking dependencies first.
     * For cascade deletion, use forceDestroy method instead.
     */
    public function destroy(string $id)
    {
        try {
            $category = Category::findOrFail($id);
            
            // Vérifier s'il y a des sous-catégories
            if($category->children()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category with subcategories. Please delete subcategories first.'
                ], 400);
            }
            
            // Vérifier s'il y a des produits associés
            if($category->products()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category with associated products. Please remove products first.'
                ], 400);
            }
            
            // Supprimer les relations avec les genres
            $category->genders()->detach();
            
            // Supprimer la catégorie
            $category->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Force delete a category and all its subcategories (cascade deletion).
     * Use with caution as this will delete all related data.
     */
    public function forceDestroy(string $id)
    {
        try {
            $category = Category::findOrFail($id);
            
            // Supprimer récursivement toutes les sous-catégories
            $this->deleteCategoryRecursively($category);
            
            return response()->json([
                'success' => true,
                'message' => 'Category and all subcategories deleted successfully'
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper method to recursively delete categories and their relationships
     */
    private function deleteCategoryRecursively(Category $category)
    {
        // Supprimer d'abord les sous-catégories
        foreach ($category->children as $child) {
            $this->deleteCategoryRecursively($child);
        }
        
        // Supprimer les relations
        $category->genders()->detach();
        $category->products()->detach();
        $category->shops()->detach();
        
        // Supprimer la catégorie
        $category->delete();
    }
}
