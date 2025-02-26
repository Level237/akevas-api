<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Models\Gender;
class ListCategoryController extends Controller
{

    public function getSubCategoriesByParentId($arrayIds,$id){
        $array = trim($arrayIds, '[]');
        $items = explode(',', $array);
        $categories = Category::whereIn('parent_id',$items)->whereHas('genders', function($query) use ($id) {
            $finalIds = [];
        if($id==4){
            $finalIds = [1, 2,3];
        }else{
            $finalIds = [$id];
        }
            $query->whereIn('genders.id', $finalIds);
        })->get();
        return response()->json(['categories'=>$categories],200);
    }
    public function showCategoryByGender($id){
        
        
        $categories = Category::whereHas('genders', function($query) use ($id) {
            $finalIds = [];
        if($id==4){
            $finalIds = [1, 2,3];
        }else{
            $finalIds = [$id];
        }
            $query->whereIn('genders.id', $finalIds);
        })->whereDoesntHave('parent')->get();
        
        //$response = CategoryResource::collection($categories);
        return response()->json(['categories'=>$categories],200);
    }
    public function getCategoryWithParentIdNull(){
        $rootCategories = Category::whereDoesntHave('parent')->get();

        
        return CategoryResource::collection($rootCategories);
    }
public function getCategoriesByGender($parentCategoryId) {
    // Récupérer la catégorie parente
    $parentCategory = Category::find($parentCategoryId);

    if (!$parentCategory) {
        return response()->json(['message' => 'Catégorie parente non trouvée'], 404);
    }

    // Récupérer les sous-catégories
    $children = $parentCategory->children;

    // Organiser les sous-catégories par genre
    $categoriesByGender = [];

    foreach ($children as $child) {
        // Vérifier si la sous-catégorie a des genres
        if ($child->genders->isNotEmpty()) {
            foreach ($child->genders as $gender) {
                $genderName = $gender->gender_name;

                if (!isset($categoriesByGender[$genderName])) {
                    $categoriesByGender[$genderName] = [];
                }

                $categoriesByGender[$genderName][] = $child;
            }
        } else {
            // Si pas de genre, ajouter l'enfant et vérifier pour ses enfants
            $categoriesByGender['sans_genre'][] = $child;

            // Récursion pour ajouter les enfants de cette sous-catégorie
            if ($child->children->isNotEmpty()) {
                // Appel de la fonction
                $response = $this->getCategoriesByGender($child->id);

                // Récupérer les données de la réponse JSON
                $categoriesData = $response->getData();
                $categoriesByGender['sans_genre'] = array_merge(
                    $categoriesByGender['sans_genre'],
                    $categoriesData->sans_genre ?? []
                );
            }
        }
    }
    
    return response()->json($categoriesByGender);
}

}
