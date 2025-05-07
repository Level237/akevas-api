<?php

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
            $table->foreignIdFor(VariationAttribute::class)
            ->constrained()
            ->restrictOnDelete()
            ->restrictOnUpdate();
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
