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
        Schema::create('country_languages', function (Blueprint $table) {
            $table->id();  // Auto-incrementing ID
            $table->unsignedBigInteger('country_id');  // Foreign key to countries table
            $table->unsignedBigInteger('language_id');  // Foreign key to languages table
            $table->boolean('status')->default(1);  // Status: active or inactive
            $table->timestamps();  // Created at and Updated at timestamps

            // Define foreign key constraints
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');  // Disable foreign key checks
        Schema::dropIfExists('country_language');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');  // Re-enable foreign key checks
    }
};
