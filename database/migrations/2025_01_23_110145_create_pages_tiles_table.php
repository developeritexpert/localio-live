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
        Schema::create('pages_tiles', function (Blueprint $table) {
            $table->id();  // auto-increment primary key
            $table->unsignedBigInteger('lang_id')->default(1);  // Foreign key for language
            $table->string('image')->nullable();  // Path to image
            $table->string('type')->nullable();  // Type of the tile (can be a category, product, etc.)
            $table->string('source')->nullable();  // Source of the tile (for example: URL or file)
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
        Schema::dropIfExists('pages_tiles');
    }
};
