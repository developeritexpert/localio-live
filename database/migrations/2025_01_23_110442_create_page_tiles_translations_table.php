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
        Schema::create('page_tiles_translations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('page_tile_id'); // Foreign key reference to page_tiles table
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->string('image')->nullable();  // Image for translation
                $table->integer('status')->default(1);  // Status for the translation
                $table->string('img')->nullable();
                $table->string('small_img')->nullable();
                $table->timestamps();
            });
       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_tiles_translations');
    }
};
