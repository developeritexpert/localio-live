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
        if (!Schema::hasTable('meta_tag_rules')) {
            Schema::create('meta_tag_rules', function (Blueprint $table) {
                $table->id();
                $table->string('entity_type', 50)->index(); // categories, businesses, category_faqs, features
                $table->unsignedBigInteger('lang_id')->default(1)->index();
                $table->integer('title_char_limit')->default(60);
                $table->string('title_primary', 255)->nullable();
                $table->string('title_secondary', 255)->nullable();
                $table->string('title_tertiary', 255)->nullable();
                $table->integer('description_char_limit')->default(160);
                $table->text('description_primary')->nullable();
                $table->text('description_secondary')->nullable();
                $table->text('description_tertiary')->nullable();
                $table->timestamps();

                $table->unique(['entity_type', 'lang_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meta_tag_rules');
    }
};
