<?php

use App\Models\ShopType;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('shop_name');
            $table->string('shop_key')->unique();
            $table->string("shop_description");
            $table->foreignIdFor(User::class)
            ->constrained()
            ->restrictOnDelete()
            ->restrictOnUpdate();
            $table->string('shop_url');
            $table->string('shop_banner')->nullable();
            $table->string('shop_profile');
            $table->string('shop_town');
            $table->string('shop_quarter');
            $table->boolean("status")->default(0);
            $table->boolean('isSubscribe')->default(0);
            $table->string('shop_product_type');
            $table->timestamp('expire')->nullable();
            $table->string('subscribe_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
