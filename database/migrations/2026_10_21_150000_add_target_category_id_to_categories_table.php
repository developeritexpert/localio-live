<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('categories', 'target_category_id')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->unsignedBigInteger('target_category_id')->nullable()->after('parent_id');
                $table->foreign('target_category_id')->references('id')->on('categories')->onDelete('set null');
            });
        }

        // 1. Merge "Digital product platforms" (Cat #230 under Creator tools #224)
        // to point to Cat #356 (under Ecommerce #64)
        $catCreator = DB::table('categories')->where('id', 230)->first();
        $catEcom = DB::table('categories')->where('id', 356)->first();

        if ($catCreator && $catEcom) {
            DB::table('categories')->where('id', 230)->update([
                'target_category_id' => 356,
                'updated_at' => now(),
            ]);

            // Ensure Cat #230 does not collide on comparison_slug with Cat #356
            DB::table('category_translations')
                ->where('category_id', 230)
                ->where('comparison_slug', 'digital-product-platforms-comparison')
                ->update(['comparison_slug' => 'digital-product-platforms-1-comparison']);
        }

        // 2. Fix other identical comparison_slugs across category_translations
        $duplicates = DB::table('category_translations')
            ->select('comparison_slug', 'lang_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('comparison_slug')
            ->where('comparison_slug', '!=', '')
            ->groupBy('comparison_slug', 'lang_id')
            ->having('total', '>', 1)
            ->get();

        foreach ($duplicates as $dup) {
            $records = DB::table('category_translations')
                ->where('comparison_slug', $dup->comparison_slug)
                ->where('lang_id', $dup->lang_id)
                ->orderBy('category_id', 'asc')
                ->get();

            // Skip the first canonical record, de-duplicate subsequent records
            $index = 1;
            foreach ($records->skip(1) as $rec) {
                $newCompSlug = $dup->comparison_slug . '-' . $index;
                while (DB::table('category_translations')
                    ->where('comparison_slug', $newCompSlug)
                    ->where('lang_id', $dup->lang_id)
                    ->exists()) {
                    $index++;
                    $newCompSlug = $dup->comparison_slug . '-' . $index;
                }

                DB::table('category_translations')
                    ->where('id', $rec->id)
                    ->update(['comparison_slug' => $newCompSlug]);

                $index++;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('categories', 'target_category_id')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropForeign(['target_category_id']);
                $table->dropColumn('target_category_id');
            });
        }
    }
};
