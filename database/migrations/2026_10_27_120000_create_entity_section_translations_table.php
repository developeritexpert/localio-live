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
        Schema::create('entity_section_translations', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 32); // category, feature, business, template
            $table->unsignedBigInteger('entity_id')->default(0); // ID of entity (0 for global template)
            $table->string('section_group', 64)->default('general');
            $table->string('field_key', 64);
            $table->text('source_headline')->nullable();
            $table->longText('source_subtext')->nullable();
            $table->string('source_hash', 64)->nullable();
            $table->unsignedBigInteger('lang_id')->default(1);
            $table->text('translated_headline')->nullable();
            $table->longText('translated_subtext')->nullable();
            $table->string('status', 32)->default('missing'); // missing, waiting, batched, completed, outdated
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->timestamps();

            $table->unique(['entity_type', 'entity_id', 'field_key', 'lang_id'], 'uniq_entity_sec_lang');
            $table->index(['entity_type', 'entity_id', 'field_key'], 'idx_entity_sec_lookup');
            $table->index(['status', 'lang_id'], 'idx_entity_sec_status_lang');
            $table->index('source_hash', 'idx_entity_sec_hash');
            $table->index('batch_id', 'idx_entity_sec_batch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entity_section_translations');
    }
};
