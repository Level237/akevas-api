<?php

use App\Models\Product;
use App\Models\AttributeValue;
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
        Schema::create('product_attributes_values', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)
                ->constrained()
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->foreignIdFor(AttributeValue::class)
                ->constrained()
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->string('price')->nullable();
            $table->string('variant_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attributes_values');
    }
};
