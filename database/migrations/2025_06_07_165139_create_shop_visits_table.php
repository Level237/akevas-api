<?php

use App\Models\Shop;
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
        Schema::disableForeignKeyConstraints();
        Schema::create('shop_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Shop::class)->constrained()->onDelete('cascade');
            $table->ipAddress('ip')->nullable(); // pour limiter les doublons
            $table->string('user_agent')->nullable(); // info navigateur
            $table->timestamp('visited_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_visits');
    }
};
