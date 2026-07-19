<?php

namespace App\Http\Resources;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $role_id=$this->user->role_id ? $role_id=$this->user->role_id : null;

       
        return [
            'id'=>$this->id,
            'message'=>$this->message,
            'user'=>$this->user,
            'role'=>$role_id,
            'shop'=>Shop::where("user_id",$this->user->id)->first(),
            'status'=>$this->status,
            'created_at'=>$this->created_at
        ];
    }
}
