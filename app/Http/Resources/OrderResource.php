<?php

namespace App\Http\Resources;

use App\Models\Town;
use App\Models\User;
use App\Models\Quarter;
use Illuminate\Http\Request;
use App\Models\OrderVariation;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\OrderDetailResource;
use App\Http\Resources\OrderVariationResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Vérifier si les orderDetails sont vides
        $orderDetails = $this->orderDetails;
        $orderVariations = $this->orderVariations;
        
        $items = $orderDetails->count() > 0 
            ? OrderDetailResource::collection($orderDetails)
            : null;
        
        $itemsCount = $orderDetails->count() + $orderVariations->count();

        return [
            'id'=>$this->id,
            'total_amount'=>$this->total,
            'orderVariations'=>$orderVariations->count() > 0 ? OrderVariationResource::collection(OrderVariation::where("order_id",$this->id)->get()) : null,
            'status'=>$this->status,
            'created_at'=>$this->created_at,
           'quarter_delivery'=>$this->quarter_delivery,
           'order_details'=>$items,
            'itemsCount'=>$itemsCount,
            'payment_method'=>$this->payment_method,
            'isPay'=>$this->isPay,
            'userName'=>$this->user->userName,
            'userPhone'=>$this->user->phone_number,
            'email'=>$this->user->email,
            'isTake'=>$this->isTake,
            //'phone'=>User::find(DB::table("delivery_order")->where('order_id',$this->id)->select('user_id')->first()->user_id)->phone_number || null,
            'fee_of_shipping'=>$this->fee_of_shipping,
            'residence'=>Quarter::find($this->user->residence)->first()->quarter_name,
            //'quater_delivery'=> $this->quarter_delivery ? Quarter::where("quarter_name", $this->quarter_delivery)->first()->quarter_name : null,
            'emplacement'=>$this->fee_of_shipping == 0 && Town::find(Quarter::find($this->user->residence)->first()->town_id)->first()->town_name=="Douala" ? "Dans les locaux de Douala" : "Dans les locaux de Yaoundé",
          
            'status'=>$this->status,
            'duration_of_delivery'=>$this->duration_of_delivery,
            "created_at"=>$this->created_at,
        ];
    }
}
