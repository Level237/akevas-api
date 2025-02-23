<?php

use App\Models\Vehicle;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Quarter;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_quarter', function (Blueprint $table) {
           $table->id();
            $table->foreignIdFor(Vehicle::class)
            ->constrained()
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->foreignIdFor(Quarter::class)
            ->constrained()
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_quarter');
    }
};
