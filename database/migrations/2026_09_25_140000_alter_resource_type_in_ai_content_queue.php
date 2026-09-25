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
        if (Schema::hasTable('ai_content_queue')) {
            DB::statement("ALTER TABLE `ai_content_queue` MODIFY `resource_type` VARCHAR(50) NOT NULL DEFAULT 'business'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ai_content_queue')) {
            DB::statement("ALTER TABLE `ai_content_queue` MODIFY `resource_type` ENUM('business', 'category', 'feature') NOT NULL DEFAULT 'business'");
        }
    }
};
