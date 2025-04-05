<?php

use App\Models\Description;
use App\Models\Subscription;
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
        Schema::create('description_subscription', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Subscription::class)
            ->constrained()
            ->cascadeOnDelete();
            $table->foreignIdFor(Description::class)
            ->constrained()
            ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('description_subscription');
    }
};
