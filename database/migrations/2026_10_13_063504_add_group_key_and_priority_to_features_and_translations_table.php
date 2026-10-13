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
        if (Schema::hasTable('features')) {
            Schema::table('features', function (Blueprint $table) {
                if (!Schema::hasColumn('features', 'group_key')) {
                    $table->string('group_key')->nullable()->after('category_id')->index();
                }
                if (!Schema::hasColumn('features', 'key_priority')) {
                    $table->tinyInteger('key_priority')->default(0)->after('group_key')->index();
                }
            });
        }

        if (Schema::hasTable('feature_translations')) {
            Schema::table('feature_translations', function (Blueprint $table) {
                if (!Schema::hasColumn('feature_translations', 'group_key')) {
                    $table->string('group_key')->nullable()->after('name')->index();
                }
                if (!Schema::hasColumn('feature_translations', 'key_priority')) {
                    $table->tinyInteger('key_priority')->default(0)->after('group_key')->index();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('features')) {
            Schema::table('features', function (Blueprint $table) {
                if (Schema::hasColumn('features', 'key_priority')) {
                    $table->dropColumn('key_priority');
                }
                if (Schema::hasColumn('features', 'group_key')) {
                    $table->dropColumn('group_key');
                }
            });
        }

        if (Schema::hasTable('feature_translations')) {
            Schema::table('feature_translations', function (Blueprint $table) {
                if (Schema::hasColumn('feature_translations', 'key_priority')) {
                    $table->dropColumn('key_priority');
                }
                if (Schema::hasColumn('feature_translations', 'group_key')) {
                    $table->dropColumn('group_key');
                }
            });
        }
    }
};
