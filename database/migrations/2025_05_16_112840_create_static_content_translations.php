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
        Schema::create('static_content_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('static_content_key_id');
            $table->unsignedBigInteger('lang_id');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['static_content_key_id', 'lang_id']);
            $table->foreign('static_content_key_id')->references('id')->on('static_content_keys')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('static_content_translations');
    }
};
