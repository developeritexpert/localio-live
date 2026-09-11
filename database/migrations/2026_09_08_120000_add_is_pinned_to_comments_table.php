<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('business_discussion_comments') && !Schema::hasColumn('business_discussion_comments', 'is_pinned')) {
            Schema::table('business_discussion_comments', function (Blueprint $table) {
                $table->boolean('is_pinned')->default(false)->after('downvotes_count');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('business_discussion_comments') && Schema::hasColumn('business_discussion_comments', 'is_pinned')) {
            Schema::table('business_discussion_comments', function (Blueprint $table) {
                $table->dropColumn('is_pinned');
            });
        }
    }
};
