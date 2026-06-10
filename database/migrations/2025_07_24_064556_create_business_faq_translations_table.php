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
        Schema::create('business_faq_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_faq_id');
            $table->unsignedBigInteger('lang_id');
            $table->string('question', 500);
            $table->text('answer');
            $table->timestamps();
            
            $table->foreign('business_faq_id')->references('id')->on('business_faqs')->onDelete('cascade');
            $table->unique(['business_faq_id', 'lang_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_faq_translations');
    }
};
