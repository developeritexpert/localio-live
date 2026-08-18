<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('category_translations', function (Blueprint $table) {
            if (!Schema::hasColumn('category_translations', 'page_title')) {
                $table->string('page_title')->nullable()->after('name');
            }
            if (!Schema::hasColumn('category_translations', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('description');
            }
            if (!Schema::hasColumn('category_translations', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('category_translations', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('category_translations', 'page_title')) {
                $columnsToDrop[] = 'page_title';
            }
            if (Schema::hasColumn('category_translations', 'meta_title')) {
                $columnsToDrop[] = 'meta_title';
            }
            if (Schema::hasColumn('category_translations', 'meta_description')) {
                $columnsToDrop[] = 'meta_description';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
