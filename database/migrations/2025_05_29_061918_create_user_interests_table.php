<?php

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
        Schema::create('user_interests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('interest_type'); // category, business, product
            $table->string('interest_id'); // ID of category/business/product
            $table->decimal('score', 8, 2)->default(0);
            $table->timestamp('last_updated');
            $table->timestamps();
            
            $table->unique(['user_id', 'interest_type', 'interest_id']);
            $table->index(['user_id', 'score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_interests');
    }
};
