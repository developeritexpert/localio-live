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
        // 1. business_translations
        if (Schema::hasTable('business_translations')) {
            Schema::table('business_translations', function (Blueprint $table) {
                if (!$this->hasIndex('business_translations', 'idx_bt_slug')) {
                    $table->index('slug', 'idx_bt_slug');
                }
                if (!$this->hasIndex('business_translations', 'idx_bt_biz_lang')) {
                    $table->index(['business_id', 'lang_id'], 'idx_bt_biz_lang');
                }
                if (!$this->hasIndex('business_translations', 'idx_bt_name')) {
                    $table->index('name', 'idx_bt_name');
                }
            });
        }

        // 2. businesses
        if (Schema::hasTable('businesses')) {
            Schema::table('businesses', function (Blueprint $table) {
                if (!$this->hasIndex('businesses', 'idx_businesses_status')) {
                    $table->index('status', 'idx_businesses_status');
                }
                if (!$this->hasIndex('businesses', 'idx_businesses_cat_status')) {
                    $table->index(['category_id', 'status'], 'idx_businesses_cat_status');
                }
                if (!$this->hasIndex('businesses', 'idx_businesses_affiliate')) {
                    $table->index('is_affiliate', 'idx_businesses_affiliate');
                }
                if (!$this->hasIndex('businesses', 'idx_businesses_created')) {
                    $table->index('created_at', 'idx_businesses_created');
                }
            });
        }

        // 3. reviews
        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                if (!$this->hasIndex('reviews', 'idx_reviews_biz_status')) {
                    $table->index(['business_id', 'status'], 'idx_reviews_biz_status');
                }
                if (!$this->hasIndex('reviews', 'idx_reviews_status_rating')) {
                    $table->index(['status', 'rating'], 'idx_reviews_status_rating');
                }
            });
        }

        // 4. review_ratings
        if (Schema::hasTable('review_ratings')) {
            Schema::table('review_ratings', function (Blueprint $table) {
                if (!$this->hasIndex('review_ratings', 'idx_rr_criteria_rating')) {
                    $table->index(['criteria_id', 'rating'], 'idx_rr_criteria_rating');
                }
                if (!$this->hasIndex('review_ratings', 'idx_rr_criteria')) {
                    $table->index('criteria_id', 'idx_rr_criteria');
                }
            });
        }

        // 5. business_country
        if (Schema::hasTable('business_country')) {
            Schema::table('business_country', function (Blueprint $table) {
                if (!$this->hasIndex('business_country', 'idx_bc_biz_country')) {
                    $table->index(['business_id', 'country_id'], 'idx_bc_biz_country');
                }
            });
        }

        // 6. business_sub_category
        if (Schema::hasTable('business_sub_category')) {
            Schema::table('business_sub_category', function (Blueprint $table) {
                if (!$this->hasIndex('business_sub_category', 'idx_bsc_biz_cat')) {
                    $table->index(['business_id', 'category_id'], 'idx_bsc_biz_cat');
                }
            });
        }

        // 7. business_websites
        if (Schema::hasTable('business_websites')) {
            Schema::table('business_websites', function (Blueprint $table) {
                if (!$this->hasIndex('business_websites', 'idx_bw_biz_country')) {
                    $table->index(['business_id', 'country_id'], 'idx_bw_biz_country');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('business_translations')) {
            Schema::table('business_translations', function (Blueprint $table) {
                if ($this->hasIndex('business_translations', 'idx_bt_slug')) {
                    $table->dropIndex('idx_bt_slug');
                }
                if ($this->hasIndex('business_translations', 'idx_bt_biz_lang')) {
                    $table->dropIndex('idx_bt_biz_lang');
                }
                if ($this->hasIndex('business_translations', 'idx_bt_name')) {
                    $table->dropIndex('idx_bt_name');
                }
            });
        }

        if (Schema::hasTable('businesses')) {
            Schema::table('businesses', function (Blueprint $table) {
                if ($this->hasIndex('businesses', 'idx_businesses_status')) {
                    $table->dropIndex('idx_businesses_status');
                }
                if ($this->hasIndex('businesses', 'idx_businesses_cat_status')) {
                    $table->dropIndex('idx_businesses_cat_status');
                }
                if ($this->hasIndex('businesses', 'idx_businesses_affiliate')) {
                    $table->dropIndex('idx_businesses_affiliate');
                }
                if ($this->hasIndex('businesses', 'idx_businesses_created')) {
                    $table->dropIndex('idx_businesses_created');
                }
            });
        }

        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                if ($this->hasIndex('reviews', 'idx_reviews_biz_status')) {
                    $table->dropIndex('idx_reviews_biz_status');
                }
                if ($this->hasIndex('reviews', 'idx_reviews_status_rating')) {
                    $table->dropIndex('idx_reviews_status_rating');
                }
            });
        }

        if (Schema::hasTable('review_ratings')) {
            Schema::table('review_ratings', function (Blueprint $table) {
                if ($this->hasIndex('review_ratings', 'idx_rr_criteria_rating')) {
                    $table->dropIndex('idx_rr_criteria_rating');
                }
                if ($this->hasIndex('review_ratings', 'idx_rr_criteria')) {
                    $table->dropIndex('idx_rr_criteria');
                }
            });
        }

        if (Schema::hasTable('business_country')) {
            Schema::table('business_country', function (Blueprint $table) {
                if ($this->hasIndex('business_country', 'idx_bc_biz_country')) {
                    $table->dropIndex('idx_bc_biz_country');
                }
            });
        }

        if (Schema::hasTable('business_sub_category')) {
            Schema::table('business_sub_category', function (Blueprint $table) {
                if ($this->hasIndex('business_sub_category', 'idx_bsc_biz_cat')) {
                    $table->dropIndex('idx_bsc_biz_cat');
                }
            });
        }

        if (Schema::hasTable('business_websites')) {
            Schema::table('business_websites', function (Blueprint $table) {
                if ($this->hasIndex('business_websites', 'idx_bw_biz_country')) {
                    $table->dropIndex('idx_bw_biz_country');
                }
            });
        }
    }
};
