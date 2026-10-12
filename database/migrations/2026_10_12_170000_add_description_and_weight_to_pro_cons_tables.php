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
        if (Schema::hasTable('category_pro_cons')) {
            Schema::table('category_pro_cons', function (Blueprint $table) {
                if (!Schema::hasColumn('category_pro_cons', 'description')) {
                    $table->text('description')->nullable()->after('text');
                }
            });
        }

        if (Schema::hasTable('business_pro_cons')) {
            Schema::table('business_pro_cons', function (Blueprint $table) {
                if (!Schema::hasColumn('business_pro_cons', 'description')) {
                    $table->text('description')->nullable()->after('text');
                }
                if (!Schema::hasColumn('business_pro_cons', 'weight')) {
                    $table->unsignedTinyInteger('weight')->default(5)->after('description');
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
                if (Schema::hasColumn('category_pro_cons', 'description')) {
                    $table->dropColumn('description');
                }
            });
        }

        if (Schema::hasTable('business_pro_cons')) {
            Schema::table('business_pro_cons', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('business_pro_cons', 'weight')) {
                    $cols[] = 'weight';
                }
                if (Schema::hasColumn('business_pro_cons', 'description')) {
                    $cols[] = 'description';
                }
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
