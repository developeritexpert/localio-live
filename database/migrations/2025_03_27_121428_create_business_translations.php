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
        Schema::create('business_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id')->nullable();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('lang_id')->nullable();
            $table->string('description')->nullable();
            $table->string('headquarters')->nullable();
            $table->string('support_options')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
                // Foreign Key Constraint
                $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_translations');
    }
};
