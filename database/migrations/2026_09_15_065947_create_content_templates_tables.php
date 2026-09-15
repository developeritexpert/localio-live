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
        Schema::create('content_templates', function (Blueprint $table) {
            $table->id();
            $table->string('section_group', 50); // category, category_faqs, features
            $table->string('field_key', 64);     // header_intro, top_10, etc.
            $table->unsignedInteger('option_number')->default(1);
            $table->text('headline')->nullable();
            $table->longText('subtext')->nullable();
            $table->unsignedBigInteger('lang_id')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['section_group', 'field_key', 'lang_id']);
            $table->index('lang_id');
        });

        Schema::create('content_template_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 32); // category, feature
            $table->unsignedBigInteger('entity_id');
            $table->unsignedBigInteger('lang_id')->default(1);
            $table->string('field_key', 64);
            $table->unsignedBigInteger('template_id');
            $table->timestamps();

            $table->foreign('template_id')->references('id')->on('content_templates')->onDelete('cascade');
            $table->unique(['entity_type', 'entity_id', 'lang_id', 'field_key'], 'uniq_entity_lang_field');
            $table->index(['entity_type', 'entity_id', 'lang_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_template_assignments');
        Schema::dropIfExists('content_templates');
    }
};