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
        if (!Schema::hasColumn('business_translations', 'description_title')) {
            Schema::table('business_translations', function (Blueprint $table) {
                $table->string('description_title')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('business_translations', 'description_title')) {
            Schema::table('business_translations', function (Blueprint $table) {
                $table->dropColumn('description_title');
            });
        }
    }
};
