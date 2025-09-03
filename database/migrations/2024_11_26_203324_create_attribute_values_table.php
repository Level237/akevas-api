<?php

use App\Models\Attribute;
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
        Schema::disableForeignKeyConstraints();
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Attribute::class)
                ->constrained()
                ->restrictOnDelete()
                ->restrictOnUpdate();

            $table->foreignId('attribute_value_group_id')
                ->nullable()
                ->constrained('attribute_value_groups')
                ->nullOnDelete();
            $table->string("value");
            $table->string("hex_color")->nullable();
            $table->string("label")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
    }
};
