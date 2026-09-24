<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modify provider column to string(50) and add input_payload if not present
        Schema::table('ai_batch_jobs', function (Blueprint $table) {
            if (!Schema::hasColumn('ai_batch_jobs', 'input_payload')) {
                $table->longText('input_payload')->nullable()->after('completed_at');
            }
        });

        // Change provider column to varchar(50) in MySQL
        DB::statement("ALTER TABLE `ai_batch_jobs` MODIFY `provider` VARCHAR(50) NOT NULL DEFAULT 'manual'");
    }

    public function down(): void
    {
        Schema::table('ai_batch_jobs', function (Blueprint $table) {
            if (Schema::hasColumn('ai_batch_jobs', 'input_payload')) {
                $table->dropColumn('input_payload');
            }
        });
    }
};
