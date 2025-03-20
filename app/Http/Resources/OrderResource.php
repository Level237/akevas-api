<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Models\Quarter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\OrderDetailResource;
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
        return [
            'id'=>$this->id,
            'total_amount'=>$this->total,
            'status'=>$this->status,
            'created_at'=>$this->created_at,
           'quarter_delivery'=>$this->quarter_delivery,
           'order_details'=>OrderDetailResource::collection($this->orderDetails)        ,
            'itemsCount'=>$this->orderDetails->count(),
            'payment_method'=>$this->payment_method,
            'isPay'=>$this->isPay,
            'userName'=>$this->user->userName,
            'userPhone'=>$this->user->phone_number,
            'email'=>$this->user->email,
            'isTake'=>$this->isTake,
            //'phone'=>User::find(DB::table("delivery_order")->where('order_id',$this->id)->select('user_id')->first()->user_id)->phone_number || null,
            'fee_of_shipping'=>$this->fee_of_shipping,
            'residence'=>Quarter::find($this->user->residence)->first()->quarter_name,
            'quater_delivery'=> $this->quarter_delivery ? Quarter::where("quarter_name", $this->quarter_delivery)->first()->quarter_name : null,
            'status'=>$this->status,
            'duration_of_delivery'=>$this->duration_of_delivery,
            "created_at"=>$this->created_at,
        ];
    }
}
