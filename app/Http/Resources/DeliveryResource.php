<?php

namespace App\Http\Resources;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Resources\VehicleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
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
            "identity_card_in_front"=>URL("/storage/".$this->identity_card_in_front),
            "isDelivery"=>$this->isDelivery,
            "identity_card_with_the_person"=>URL("/storage/".$this->identity_card_with_the_person),
            "vehicle"=>VehicleResource::make(Vehicle::where('user_id',$this->id)->first()),
            "created_at"=>$this->created_at
        ];
    }
}
