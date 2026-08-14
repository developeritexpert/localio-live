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
        if (Schema::hasTable('review_translations') && !Schema::hasColumn('review_translations', 'pros')) {
            Schema::table('review_translations', function (Blueprint $table) {
                $table->text('pros')->nullable()->after('description');
                $table->text('cons')->nullable()->after('pros');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('review_translations') && Schema::hasColumn('review_translations', 'pros')) {
            Schema::table('review_translations', function (Blueprint $table) {
                $table->dropColumn(['pros', 'cons']);
            });
        }
    }
};
