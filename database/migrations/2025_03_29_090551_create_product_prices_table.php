<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->string('currency', 10);
            $table->enum('time_unit', ['day', 'week', 'month', 'quarter', 'year', 'one_time'])->default('month');
            $table->string('additional_info')->nullable();

            // Discount fields
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->date('discount_expiration_date')->nullable();
            $table->enum('discount_time_units', ['day', 'week', 'month', 'quarter', 'year', 'one_time'])->nullable();

            // Renewal price
            $table->decimal('renewal_price', 10, 2)->nullable();
            $table->enum('renewal_time_units', ['day', 'week', 'month', 'quarter', 'year', 'one_time'])->nullable();

            $table->timestamps();

            // Ensure each product can only have one price per country
            $table->unique(['product_id', 'time_unit']);
       });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('product_prices');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
