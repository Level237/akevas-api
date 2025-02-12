<?php

use App\Models\Role;
use App\Models\Town;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('userName')->unique()->nullable();
            $table->string('firstName')->nullable();
            $table->string('lastName')->nullable();
            $table->string('birthDate')->nullable();
            $table->string('nationality')->nullable();
            $table->foreignIdFor(Role::class)
            ->constrained()
            ->restrictOnDelete()
            ->restrictOnUpdate();
            
            $table->string('phone_number')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('isWholesaler')->nullable();
            $table->string('identity_card_in_front')->nullable();
            $table->string('identity_card_in_back')->nullable();
            $table->string('identity_card_with_the_person')->nullable();
            $table->string('profile')->nullable();
            $table->boolean('isSeller')->default(0);
            $table->boolean('isDelivery')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
