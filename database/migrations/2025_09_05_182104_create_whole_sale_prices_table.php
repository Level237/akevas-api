<?php

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
        Schema::create('whole_sale_prices', function (Blueprint $table) {
            $table->id();

            $table->string('priceable_id');
            $table->string('priceable_type');

            $table->string('min_quantity');
            $table->string("wholesale_price");

            $table->timestamps();

            $table->index(['priceable_id', 'priceable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whole_sale_prices');
    }
};
