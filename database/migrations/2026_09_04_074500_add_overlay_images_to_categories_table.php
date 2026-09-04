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
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'category_overlay_image')) {
                $table->unsignedBigInteger('category_overlay_image')->nullable()->after('top_rated_image');
                $table->string('category_overlay_position', 20)->default('right')->after('category_overlay_image');
            }
            if (!Schema::hasColumn('categories', 'faq_overlay_image')) {
                $table->unsignedBigInteger('faq_overlay_image')->nullable()->after('category_overlay_position');
                $table->string('faq_overlay_position', 20)->default('right')->after('faq_overlay_image');
            }
            if (!Schema::hasColumn('categories', 'comparison_overlay_image')) {
                $table->unsignedBigInteger('comparison_overlay_image')->nullable()->after('faq_overlay_position');
                $table->string('comparison_overlay_position', 20)->default('right')->after('comparison_overlay_image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $colsToDrop = [];
            foreach ([
                'category_overlay_image',
                'category_overlay_position',
                'faq_overlay_image',
                'faq_overlay_position',
                'comparison_overlay_image',
                'comparison_overlay_position'
            ] as $col) {
                if (Schema::hasColumn('categories', $col)) {
                    $colsToDrop[] = $col;
                }
            }
            if (!empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });
    }
};
