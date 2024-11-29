<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class NewSellerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 400));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'userName'=>'required|unique:users,userName',
            'town_id'=>'required',
            'phone_number'=>'required|unique:users,phone_number',
            'email'=>'required|unique:users,email',
            'isWholesaler'=>'required',
            'cni_in_front'=>'required',
            'cni_in_back'=>'required',
            'profile'=>'required'
        ];
    }


}
