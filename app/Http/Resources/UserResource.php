<?php

namespace App\Http\Resources;

use App\Models\Town;
use App\Models\Quarter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'birthDate'=>$this->birthDate,
            'drivers_license'=>$this->drivers_license,
            'email'=>$this->email,
            'firstName'=>$this->firstName,
            "identity_card_in_back"=>$this->identity_card_in_back,
            "identity_card_in_front"=>$this->identity_card_in_front,
            "identity_card_with_the_person"=>$this->identity_card_with_the_person,
            "isDelivery"=>$this->isDelivery,
            "isSeller"=>$this->isSeller,
            "isWholesaler"=>$this->isWholesaler,
            "lastName"=>$this->lastName,
            "nationality"=>$this->nationality,
            "phone_number"=>$this->phone_number,
            "profile"=>$this->profile,
            "role_id"=>$this->role_id,
            "userName"=>$this->userName,
            "residence"=>Town::
            where('id',
            Quarter::where("id",intval($this->residence))->select('town_id')->first()->town_id)
            ->select('town_name')->first()->town_name,
            "updated_at"=>$this->updated_at,
            "created_at"=>$this->created_at,
        ];
    }
}
