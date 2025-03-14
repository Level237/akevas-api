<?php

namespace App\Http\Resources;

use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Resources\ShopResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
class SellerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id,
            "firstName"=>$this->firstName,
            'email'=>$this->email,
            "lastName"=>$this->lastName,
            "birthDate"=>$this->birthDate,
            "nationality"=>$this->nationality,
            "role_id"=>$this->role_id,
            "phone_number"=>$this->phone_number,
            "isWholesaler"=>$this->isWholesaler,
            "identity_card_in_front"=>URL("/storage/".$this->identity_card_in_front),
            "identity_card_in_back"=>URL("/storage/".$this->identity_card_in_back),
            "identity_card_with_the_person"=>URL("/storage/".$this->identity_card_with_the_person),
            "isSeller"=>$this->isSeller,
            "feedbacks"=>$this->feedbacks,
            "shop"=>ShopResource::make(Shop::where('user_id',$this->id)->first()),
            "created_at"=>$this->created_at
        ];
    }
}
