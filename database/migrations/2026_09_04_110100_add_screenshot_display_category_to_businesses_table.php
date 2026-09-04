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
        if (!Schema::hasColumn('businesses', 'screenshot_display_category')) {
            Schema::table('businesses', function (Blueprint $table) {
                $table->string('screenshot_display_category')
                    ->nullable()
                    ->after('screenshot_categories');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('businesses', 'screenshot_display_category')) {
            Schema::table('businesses', function (Blueprint $table) {
                $table->dropColumn('screenshot_display_category');
            });
        }
    }
};
