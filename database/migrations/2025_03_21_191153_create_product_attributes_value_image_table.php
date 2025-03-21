<?php

use App\Models\Image;
use App\Models\ProductAttributesValue;
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
        Schema::create('product_attributes_value_image', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ProductAttributesValue::class)
                ->constrained()
                ->restrictOnDelete()
                ->restrictOnUpdate();
            $table->foreignIdFor(Image::class)
                ->constrained()
                ->restrictOnDelete()
                ->restrictOnUpdate();
           
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attributes_value_image');
    }
};
