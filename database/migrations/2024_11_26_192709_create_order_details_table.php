<?php

use App\Models\Order;
use App\Models\Product;
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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class)
            ->constrained()
            ->restrictOnDelete()
            ->restrictOnUpdate();
            $table->foreignIdFor(Product::class)
            ->constrained()
            ->restrictOnDelete()
            ->restrictOnUpdate();
            $table->string('order_product_quantity');
            $table->string("unit_price");
            $table->boolean('has_variation')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
