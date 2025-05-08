<?php

use App\Models\OrderDetail;
use App\Models\VariationAttribute;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(OrderDetail::class)
            ->constrained()
            ->restrictOnDelete()
            ->restrictOnUpdate();
            $table->unsignedBigInteger('variation_attribute_id')->nullable();
            $table->foreign('variation_attribute_id')->references('id')->on('variation_attributes')->onDelete('cascade');
            
            $table->unsignedBigInteger('product_variation_id')->nullable();
            $table->foreign('product_variation_id')->references('id')->on('product_variations')->onDelete('cascade');
            
            $table->string('variation_quantity');
            $table->string('variation_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_variations');
    }
};
