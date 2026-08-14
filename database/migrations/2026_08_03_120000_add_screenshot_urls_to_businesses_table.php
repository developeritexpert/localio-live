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
        if (!Schema::hasColumn('businesses', 'screenshot_urls')) {
            Schema::table('businesses', function (Blueprint $table) {
            $table->json('screenshot_urls')->nullable()->after('business_images');
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('businesses', 'screenshot_urls')) {
            Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn('screenshot_urls');
        });
        }
    }
};
