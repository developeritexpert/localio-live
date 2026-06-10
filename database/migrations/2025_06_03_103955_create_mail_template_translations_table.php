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
        Schema::create('mail_template_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mail_template_id')->constrained('mail_templates')->onDelete('cascade');
            $table->unsignedBigInteger('lang_id');
            $table->string('subject');
            $table->longText('body');
            $table->timestamps();

            $table->unique(['mail_template_id', 'lang_id']);
            $table->index(['mail_template_id', 'lang_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_template_translations');
    }
};
