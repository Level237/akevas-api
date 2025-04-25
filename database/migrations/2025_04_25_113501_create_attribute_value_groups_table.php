<?php

use App\Models\Attribute;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {Schema::disableForeignKeyConstraints();
        Schema::create('attribute_value_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Attribute::class)
            ->constrained()
            ->restrictOnDelete()
            ->restrictOnUpdate();

             $table->string('label'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_value_groups');
    }
};
