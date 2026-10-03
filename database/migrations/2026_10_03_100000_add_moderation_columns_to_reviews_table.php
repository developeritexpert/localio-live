<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Expand ENUM to include all old+new values (safest MySQL approach)
        DB::statement("ALTER TABLE reviews MODIFY COLUMN status ENUM('active','inactive','processing','published','pending_review','rejected','removed') NOT NULL DEFAULT 'processing'");

        // Step 2: Migrate existing data
        DB::statement("UPDATE reviews SET status = 'published' WHERE status = 'active'");
        DB::statement("UPDATE reviews SET status = 'pending_review' WHERE status = 'inactive'");

        // Step 3: Remove old values, keep only new statuses
        DB::statement("ALTER TABLE reviews MODIFY COLUMN status ENUM('processing','published','pending_review','rejected','removed') NOT NULL DEFAULT 'processing'");

        // Step 4: Add all new moderation columns
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('laravel_check_status')->nullable()->after('status');
            $table->string('laravel_fail_reason')->nullable()->after('laravel_check_status');
            $table->boolean('moderation_api_passed')->nullable()->after('laravel_fail_reason');
            $table->json('moderation_api_result')->nullable()->after('moderation_api_passed');
            $table->timestamp('moderation_api_checked_at')->nullable()->after('moderation_api_result');
            $table->string('localio_ai_status')->default('waiting')->after('moderation_api_checked_at');
            $table->string('localio_ai_batch_id')->nullable()->after('localio_ai_status');
            $table->string('localio_ai_decision')->nullable()->after('localio_ai_batch_id');
            $table->string('localio_ai_risk_level')->nullable()->after('localio_ai_decision');
            $table->json('localio_ai_flags')->nullable()->after('localio_ai_risk_level');
            $table->decimal('localio_ai_confidence', 5, 2)->nullable()->after('localio_ai_flags');
            $table->text('localio_ai_reason')->nullable()->after('localio_ai_confidence');
            $table->string('submitted_locale', 20)->nullable()->after('localio_ai_reason');
            $table->string('source_locale', 20)->nullable()->after('submitted_locale');
            $table->string('locale_status', 20)->nullable()->after('source_locale');
            $table->string('localio_ai_model')->nullable()->after('locale_status');
            $table->string('localio_ai_prompt_id')->nullable()->after('localio_ai_model');
            $table->string('localio_ai_prompt_version')->nullable()->after('localio_ai_prompt_id');
            $table->timestamp('localio_ai_processed_at')->nullable()->after('localio_ai_prompt_version');
            $table->string('escalation_source')->nullable()->after('localio_ai_processed_at');
            $table->string('appeal_status')->nullable()->after('escalation_source');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn([
                'laravel_check_status','laravel_fail_reason','moderation_api_passed',
                'moderation_api_result','moderation_api_checked_at','localio_ai_status',
                'localio_ai_batch_id','localio_ai_decision','localio_ai_risk_level',
                'localio_ai_flags','localio_ai_confidence','localio_ai_reason',
                'submitted_locale','source_locale','locale_status','localio_ai_model',
                'localio_ai_prompt_id','localio_ai_prompt_version','localio_ai_processed_at',
                'escalation_source','appeal_status',
            ]);
        });
        DB::statement("UPDATE reviews SET status = 'active' WHERE status = 'published'");
        DB::statement("UPDATE reviews SET status = 'inactive' WHERE status IN ('processing','pending_review','rejected','removed')");
        DB::statement("ALTER TABLE reviews MODIFY COLUMN status ENUM('active','inactive') NOT NULL DEFAULT 'active'");
    }
};
