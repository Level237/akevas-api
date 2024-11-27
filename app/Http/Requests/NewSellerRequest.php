<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewSellerRequest extends FormRequest
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
            'userName'=>'required|exists:users,userName',
            'town_id'=>'required',
            'phone_number'=>'required',
            'isWholesaler'=>'required',
            'cni_in_front'=>'required',
            'cni_in_back'=>'required',
            'profile'=>'required'
        ];
    }
}
