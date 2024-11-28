<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_name'=>'required',
            'product_description'=>'required',
            'shop_id'=>'required',
            'product_price'=>'required',
            'product_quantity'=>'required',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'attributs' => 'array|max:7', // Limite le nombre total d'attributs
            'attributs.*' => 'exists:attribute_values,id', // Vérifie que l'attribut existe
        ];
    }
}
