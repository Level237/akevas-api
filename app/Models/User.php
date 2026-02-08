<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Shop;
use App\Models\Order;
use App\Models\Review;
use App\Models\History;
use App\Models\Payment;
use App\Models\Vehicle;
use App\Models\FeedBack;
use App\Models\ShopReview;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'userName',
        "firstName",
        "lastName",
        "birthDate",
        "nationality",
        "isWholesaler",
        "identity_card_in_front",
        'identity_card_in_back',
        "identity_card_with_the_person",

        'role_id',
        'town_id',
        'phone_number',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
    public function role(): BelongsTo
    {
        // On précise 'role_id' car c'est le nom de la colonne dans ta table 'users'
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function findForPassport($username)
    {
        return $this->where('phone_number', '=', $username)->first();
    }

    public function shop()
    {
        return $this->hasOne(Shop::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function processOrders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'delivery_order', 'user_id', 'order_id')->withPivot('isAccepted');
    }
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function hystories()
    {
        return $this->hasMany(History::class);
    }
    public function feedbacks()
    {

        return $this->hasMany(FeedBack::class);
    }
    public function shopReviews()
    {
        return $this->hasMany(ShopReview::class);
    }

    public function subscriptionUsers(): HasMany
    {
        return $this->hasMany(SubscriptionUser::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
