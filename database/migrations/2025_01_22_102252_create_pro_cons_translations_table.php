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
        Schema::create('pro_cons_translations', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('pro_cons_id'); // Foreign key to pro_cons table
            $table->unsignedBigInteger('language_id')->default(1); // Set a default language ID
            $table->string('name'); // Translation name
            $table->text('description'); // Translation description
            $table->string('type');
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_cons_translations');
    }
};
