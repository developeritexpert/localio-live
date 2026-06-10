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
        Schema::create('help_center_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('help_center_content_id');
            $table->string('image')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->foreign('help_center_content_id')->references('id')->on('help_center_contents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('help_center_categories');
    }
};
