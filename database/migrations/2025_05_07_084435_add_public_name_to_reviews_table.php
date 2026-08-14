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
        if (!Schema::hasColumn('reviews', 'public_name')) {
            Schema::table('reviews', function (Blueprint $table) {
            $table->string('public_name')->nullable()->after('status');

        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('reviews', 'public_name')) {
            Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('public_name');
        });
        }
    }
};
