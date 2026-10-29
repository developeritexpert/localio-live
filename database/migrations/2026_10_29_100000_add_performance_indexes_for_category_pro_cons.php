<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Check if index exists on table in current database.
     */
    protected function hasIndex(string $table, string $indexName): bool
    {
        $dbName = Schema::getConnection()->getDatabaseName();
        $result = DB::select(
            "SELECT COUNT(1) AS cnt FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?",
            [$dbName, $table, $indexName]
        );
        return !empty($result[0]->cnt);
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('category_pro_cons')) {
            Schema::table('category_pro_cons', function (Blueprint $table) {
                if (!$this->hasIndex('category_pro_cons', 'idx_cpc_cat_type_status')) {
                    $table->index(['category_id', 'type', 'status'], 'idx_cpc_cat_type_status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('category_pro_cons')) {
            Schema::table('category_pro_cons', function (Blueprint $table) {
                if ($this->hasIndex('category_pro_cons', 'idx_cpc_cat_type_status')) {
                    $table->dropIndex('idx_cpc_cat_type_status');
                }
            });
        }
    }
};

