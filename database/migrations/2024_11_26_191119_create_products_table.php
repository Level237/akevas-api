<?php

use App\Models\Shop;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('product_name');
            $table->text('product_description');
            $table->foreignIdFor(Shop::class)
                ->constrained()

                ->restrictOnDelete()
                ->restrictOnUpdate();

            $table->string('product_url')->nullable();
            $table->string('product_price')->nullable();
            $table->boolean('is_wholesale')->default(0);
            $table->boolean('is_only_wholesale')->default(0);
            $table->string('product_quantity')->nullable();
            $table->string('product_profile')->nullable();
            $table->boolean("status")->default(0);
            $table->boolean("isRejet")->default(0);
            $table->boolean('isSubscribe')->default(0);
            $table->timestamp('expire')->nullable();
            $table->string('subscribe_id')->nullable();
            $table->string('product_gender');
            $table->string('whatsapp_number')->nullable();
            $table->string('product_residence')->nullable();
            $table->boolean('is_trashed')->default(0);
            $table->boolean('type')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
