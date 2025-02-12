<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Shop;
use App\Models\Image;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('image_shop', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Shop::class)
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
        Schema::dropIfExists('image_shop');
    }
};
