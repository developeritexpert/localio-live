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
        // 1. feature_translations indexes
        if (Schema::hasTable('feature_translations')) {
            Schema::table('feature_translations', function (Blueprint $table) {
                if (!$this->hasIndex('feature_translations', 'idx_ft_feat_lang')) {
                    $table->index(['feature_id', 'lang_id'], 'idx_ft_feat_lang');
                }
                if (!$this->hasIndex('feature_translations', 'idx_ft_lang_name')) {
                    $table->index(['lang_id', 'name'], 'idx_ft_lang_name');
                }
            });
        }

        // 2. features table indexes
        if (Schema::hasTable('features')) {
            Schema::table('features', function (Blueprint $table) {
                if (!$this->hasIndex('features', 'idx_features_cat_status')) {
                    $table->index(['category_id', 'status'], 'idx_features_cat_status');
                }
                if (!$this->hasIndex('features', 'idx_features_status_id')) {
                    $table->index(['status', 'id'], 'idx_features_status_id');
                }
            });
        }

        // 3. content_template_assignments indexes
        if (Schema::hasTable('content_template_assignments')) {
            Schema::table('content_template_assignments', function (Blueprint $table) {
                if (!$this->hasIndex('content_template_assignments', 'idx_cta_type_lang_entity')) {
                    $table->index(['entity_type', 'lang_id', 'entity_id'], 'idx_cta_type_lang_entity');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('feature_translations')) {
            Schema::table('feature_translations', function (Blueprint $table) {
                if ($this->hasIndex('feature_translations', 'idx_ft_feat_lang')) {
                    $table->dropIndex('idx_ft_feat_lang');
                }
                if ($this->hasIndex('feature_translations', 'idx_ft_lang_name')) {
                    $table->dropIndex('idx_ft_lang_name');
                }
            });
        }

        if (Schema::hasTable('features')) {
            Schema::table('features', function (Blueprint $table) {
                if ($this->hasIndex('features', 'idx_features_cat_status')) {
                    $table->dropIndex('idx_features_cat_status');
                }
                if ($this->hasIndex('features', 'idx_features_status_id')) {
                    $table->dropIndex('idx_features_status_id');
                }
            });
        }

        if (Schema::hasTable('content_template_assignments')) {
            Schema::table('content_template_assignments', function (Blueprint $table) {
                if ($this->hasIndex('content_template_assignments', 'idx_cta_type_lang_entity')) {
                    $table->dropIndex('idx_cta_type_lang_entity');
                }
            });
        }
    }
};
