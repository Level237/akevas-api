<?php

namespace App\Http\Controllers;

use App\Models\Gender;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\CategoryResource;

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
    public function getCategoryWithParentIdNull(Request $request){
        // Vérifier si un genre est spécifié dans la requête
        if ($request->has('gender') && $request->gender) {
            $genderId = $request->gender;
            
            $rootCategories = Cache::remember('root_categories_gender_' . $genderId, 60, function () use ($genderId) {
                return Category::whereDoesntHave('parent')
                    ->whereHas('genders', function($query) use ($genderId) {
                        $finalIds = [];
                        if($genderId == 4){
                            $finalIds = [1, 2, 3];
                        } else {
                            $finalIds = [$genderId];
                        }
                        $query->whereIn('genders.id', $finalIds);
                    })->take(8)->get();
            });
        } else {
            $rootCategories = Cache::remember('root_categories', 60, function () {
                return Category::whereDoesntHave('parent')->take(8)->get();
            });
        }

        
        return CategoryResource::collection($rootCategories);
    }
public function getCategoriesByGender($parentCategoryId,Request $request) {
    $parentCategory = Cache::remember('parent_category_'.$parentCategoryId, 60, function () use ($parentCategoryId) {
        return Category::find($parentCategoryId);
    });

    if (!$parentCategory) {
        return response()->json(['message' => 'Catégorie parente non trouvée'], 404);
    }

    $children = Cache::remember('children_category_'.$parentCategoryId, 60, function () use ($parentCategory) {
        return $parentCategory->children;
    });

    if ($request->has('gender') && $request->gender) {
        $genderId = (int) $request->gender;
        $finalIds = $genderId === 4 ? [1, 2, 3] : [$genderId];

        $filtered = $children->filter(function ($child) use ($finalIds) {
            return $child->genders()->whereIn('genders.id', $finalIds)->exists();
        })->values();

        // Charger la relation genders pour la réponse
        $filtered->load('genders');

        return response()->json(['categories' => $filtered], 200);
    }

    $categoriesByGender = [];

    foreach ($children as $child) {
        if ($child->genders->isNotEmpty()) {
            foreach ($child->genders as $gender) {
                $genderName = $gender->gender_name;

                if (!isset($categoriesByGender[$genderName])) {
                    $categoriesByGender[$genderName] = [];
                }

                $categoriesByGender[$genderName][] = $child;
            }
        } else {
            $categoriesByGender['sans_genre'][] = $child;

            if ($child->children->isNotEmpty()) {
                $response = $this->getCategoriesByGender($child->id, new Request());

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

public function getCategoriesWithAttributes(){

    $categoriesWithAttributes=DB::table('categories')
    ->join('category_atributes','categories.id','=','category_atributes.category_id')
    ->join('attributes','category_atributes.attribute_id','attributes.id')
    ->select('categories.id as category_id','categories.category_name as category_name','attributes.id as attribute_id','attributes.attributes_name as attribute_name')
    ->get();

    return $categoriesWithAttributes;
}

public function getAttributeValueByAttributeId($attributeId){

   $categoryAttributeLink = DB::table('category_atributes')
        ->where('attribute_id', $attributeId)
        ->first();

    if (!$categoryAttributeLink) {
        return response()->json(['error' => 'No link found between this category and attribute.'], 404);
    }
    
    // On trouve tous les groupes de valeurs liés à cette combinaison de catégorie et d'attribut.
    $groups = DB::table('category_atribute_group_links')
        ->where('category_attribute_id', $categoryAttributeLink->id)
        ->join('attribute_value_groups', 'category_atribute_group_links.attribute_value_group_id', '=', 'attribute_value_groups.id')
        ->select('attribute_value_groups.id', 'attribute_value_groups.label')
        ->get();

    $result = [];
    
    // Pour chaque groupe trouvé, on récupère les valeurs correspondantes.
    foreach ($groups as $group) {
        $values = DB::table('attribute_values')
            ->where('attribute_value_group_id', $group->id)
            ->where('attribute_id',$attributeId)
            ->select('id', 'value','label')
            ->get();
        
        $result[] = [
            'group_id' => $group->id,
            'group_label' => $group->label,
            'values' => $values,
        ];
    }
    
    return response()->json($result);
}
}
