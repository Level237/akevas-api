<?php

use App\Models\ShopType;
use App\Models\User;
use App\Models\Town;
use App\Models\Quarter;
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
            $table->text("shop_description");
            $table->string('shop_gender');
            $table->foreignIdFor(User::class)
            ->constrained()
            ->restrictOnDelete()
            ->restrictOnUpdate();
            $table->foreignIdFor(Town::class)
            ->constrained()
            ->restrictOnDelete()
            ->restrictOnUpdate();
            $table->foreignIdFor(Quarter::class)
            ->constrained()
            ->restrictOnDelete()
            ->restrictOnUpdate();
            $table->string('coins')->default(0);
            $table->string('product_type');
            $table->string('shop_banner')->nullable();
            $table->string('shop_profile');
            $table->boolean("isPublished")->default(0);
            $table->boolean('isSubscribe')->default(0);
            $table->timestamp('expire')->nullable();
            $table->string('subscribe_id')->nullable();
            $table->string('shop_level')->default(1);
            $table->string('state')->default(0);
            $table->string("accountId")->nullable();
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
