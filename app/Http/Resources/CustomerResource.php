<?php

namespace App\Http\Resources;

use App\Models\Quarter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'userName'=>$this->userName,
            'email'=>$this->email,
            'phone_number'=>$this->phone_number,
            'residence'=>Quarter::find($this->residence)->quarter_name,
            'created_at'=>$this->created_at->format('Y-m-d'),
        ];
    }
}
