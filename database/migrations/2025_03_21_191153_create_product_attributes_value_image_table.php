<?php


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
            $table->foreignId('attributes_id')
                ->on('product_attributes_values')
                ->constrained()
                ->OnDelete('cascade')
                ->OnUpdate('cascade');
            $table->foreignId('image_id')
                ->on('images')
                ->constrained()
                ->OnDelete('cascade')
                ->OnUpdate('cascade');
           
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
