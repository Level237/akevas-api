<?php

use App\Models\AttributeValue;
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
            $table->string('image_path')->nullable();
            $table->string('price')->nullable();
            $table->string('quantity')->nullable();
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
