<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('business_discussions')) {
            DB::statement('ALTER TABLE business_discussions MODIFY business_id BIGINT UNSIGNED NULL;');

            if (!Schema::hasColumn('business_discussions', 'category_id')) {
                Schema::table('business_discussions', function (Blueprint $table) {
                    $table->unsignedBigInteger('category_id')->nullable()->after('business_id')->index();
                    $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('business_discussions') && Schema::hasColumn('business_discussions', 'category_id')) {
            Schema::table('business_discussions', function (Blueprint $table) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            });
        }
    }
};
