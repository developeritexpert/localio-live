<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create translation_batches table
        if (!Schema::hasTable('translation_batches')) {
            Schema::create('translation_batches', function (Blueprint $table) {
                $table->id();
                $table->string('openai_batch_id', 100)->nullable()->index();
                $table->string('type', 50)->default('reviews')->index();
                $table->string('model', 100)->default('gpt-5.6-luna');
                $table->string('prompt_id', 100)->nullable();
                $table->string('prompt_version', 20)->default('1');
                $table->unsignedInteger('request_count')->default(0);
                $table->string('status', 50)->default('waiting')->index();
                $table->unsignedInteger('successful_count')->default(0);
                $table->unsignedInteger('failed_count')->default(0);
                $table->timestamp('submitted_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamp('failed_at')->nullable();
                $table->timestamps();
            });
        }

        // 2. Enhance review_translations table
        if (Schema::hasTable('review_translations')) {
            Schema::table('review_translations', function (Blueprint $table) {
                if (!Schema::hasColumn('review_translations', 'review_id')) {
                    $table->unsignedBigInteger('review_id')->nullable()->after('reviews_id')->index();
                }
                if (!Schema::hasColumn('review_translations', 'source_locale')) {
                    $table->string('source_locale', 20)->nullable()->after('language_id')->index();
                }
                if (!Schema::hasColumn('review_translations', 'target_locale')) {
                    $table->string('target_locale', 20)->nullable()->after('source_locale')->index();
                }
                if (!Schema::hasColumn('review_translations', 'translated_title')) {
                    $table->string('translated_title', 255)->nullable()->after('title');
                }
                if (!Schema::hasColumn('review_translations', 'translated_text')) {
                    $table->longText('translated_text')->nullable()->after('description');
                }
                if (!Schema::hasColumn('review_translations', 'status')) {
                    $table->string('status', 50)->default('WAITING')->after('translated_text')->index();
                }
                if (!Schema::hasColumn('review_translations', 'batch_id')) {
                    $table->unsignedBigInteger('batch_id')->nullable()->after('status')->index();
                }
                if (!Schema::hasColumn('review_translations', 'openai_custom_id')) {
                    $table->string('openai_custom_id', 100)->nullable()->after('batch_id')->index();
                }
                if (!Schema::hasColumn('review_translations', 'model')) {
                    $table->string('model', 100)->default('gpt-5.6-luna')->after('openai_custom_id');
                }
                if (!Schema::hasColumn('review_translations', 'prompt_id')) {
                    $table->string('prompt_id', 100)->nullable()->after('model');
                }
                if (!Schema::hasColumn('review_translations', 'prompt_version')) {
                    $table->string('prompt_version', 20)->default('1')->after('prompt_id');
                }
                if (!Schema::hasColumn('review_translations', 'retry_count')) {
                    $table->unsignedInteger('retry_count')->default(0)->after('prompt_version');
                }
                if (!Schema::hasColumn('review_translations', 'translated_at')) {
                    $table->timestamp('translated_at')->nullable()->after('retry_count');
                }
            });

            // Sync review_id from reviews_id for existing records
            DB::statement('UPDATE review_translations SET review_id = reviews_id WHERE review_id IS NULL AND reviews_id IS NOT NULL');

            // Mark existing records as ORIGINAL if they don't have a target_locale
            DB::statement("UPDATE review_translations SET status = 'ORIGINAL' WHERE target_locale IS NULL AND (status IS NULL OR status = 'WAITING')");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('translation_batches')) {
            Schema::dropIfExists('translation_batches');
        }

        if (Schema::hasTable('review_translations')) {
            Schema::table('review_translations', function (Blueprint $table) {
                $columns = [
                    'review_id', 'source_locale', 'target_locale', 'translated_title',
                    'translated_text', 'status', 'batch_id', 'openai_custom_id',
                    'model', 'prompt_id', 'prompt_version', 'retry_count', 'translated_at'
                ];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('review_translations', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
