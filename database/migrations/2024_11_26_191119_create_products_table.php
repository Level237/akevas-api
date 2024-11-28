<?php

use App\Models\Shop;
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
        Schema::disableForeignKeyConstraints();
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('product_description');
            $table->foreignIdFor(Shop::class)
            ->constrained()
            ->restrictOnDelete()
            ->restrictOnUpdate();
            $table->string('product_url');
            $table->string('product_price');
            $table->string('product_quantity');
            $table->boolean("status")->default(0);
            $table->boolean('isSubscribe')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
