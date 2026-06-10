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
        Schema::create('faq_category_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(column: 'faq_category_id');
            $table->foreign('faq_category_id')
            ->references('id')
            ->on('faqs_categories')
            ->onDelete('cascade');
            $table->unsignedBigInteger('lang_id'); // or string if you use 'locale'
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();

            $table->unique(['faq_category_id', 'lang_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faq_category_translations');
    }
};
