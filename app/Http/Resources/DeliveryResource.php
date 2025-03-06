<?php

namespace App\Http\Resources;

use App\Models\Town;
use App\Models\Quarter;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Resources\OrderResource;
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
            "drivers_license"=>URL("/storage/".$this->drivers_license),
            "role_id"=>$this->role_id,
            "phone_number"=>$this->phone_number,
            "identity_card_in_front"=>URL("/storage/".$this->identity_card_in_front),
            "isDelivery"=>$this->isDelivery,
            "identity_card_with_the_person"=>URL("/storage/".$this->identity_card_with_the_person),
            "residence"=>Town::
            where('id',
            Quarter::where("id",intval($this->residence))->select('town_id')->first()->town_id)
            ->select('town_name')->first()->town_name,
            "vehicle"=>VehicleResource::make(Vehicle::where('user_id',$this->id)->first()),
            "myOrders"=>OrderResource::collection($this->orders),
            "ordersCount"=>$this->orders->count(),
            "created_at"=>$this->created_at
        ];
    }
}
