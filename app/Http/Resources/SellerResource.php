<?php

namespace App\Http\Resources;

use App\Models\Shop;
use App\Models\FeedBack;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Http\Resources\ShopResource;
use Illuminate\Http\Resources\Json\JsonResource;

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
            "id" => $this->id,
            "firstName" => $this->firstName,
            'email' => $this->email,
            "notifications_is_count" => $this->notifications()->where('read_at', null)->count(),
            "lastName" => $this->lastName,
            "birthDate" => $this->birthDate,
            "nationality" => $this->nationality,
            "isWholesaler" => $this->isWholesaler,
            "role_id" => $this->role_id,
            "phone_number" => $this->phone_number,
            "identity_card_in_front" => URL("/storage/" . $this->identity_card_in_front),
            "identity_card_in_back" => URL("/storage/" . $this->identity_card_in_back),
            "identity_card_with_the_person" => URL("/storage/" . $this->identity_card_with_the_person),
            "isSeller" => $this->isSeller,
            "feedbacks" => $this->feedbacks,
            "last_feedbacks_product_verification" => FeedBack::where('user_id', $this->id)->where('type', '1')->where('status', 0)->count(),
            "shop" => ShopResource::make(Shop::where('user_id', $this->id)->first()),
            "created_at" => $this->created_at
        ];
    }
}
