<?php

namespace App\Services;
use Illuminate\Support\Str;

class GenerateUrlResource{

    public function generateUrl($resource){
            // Convertir en minuscules
        $slug = Str::slug($resource, '-');
        return $slug;
    }
}
