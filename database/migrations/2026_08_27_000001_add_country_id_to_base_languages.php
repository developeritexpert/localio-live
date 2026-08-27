<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add country_id column to base_languages table if not exists
        if (!Schema::hasColumn('base_languages', 'country_id')) {
            Schema::table('base_languages', function (Blueprint $table) {
                $table->unsignedBigInteger('country_id')->nullable()->after('id');
            });
        }

        // 2. Try to map existing base languages to countries by name
        foreach (DB::table('base_languages')->get() as $bl) {
            $country = DB::table('countries')->where('name', $bl->name)->first();
            if ($country) {
                DB::table('base_languages')->where('id', $bl->id)->update(['country_id' => $country->id]);
            }
        }

        // 3. Delete the obsolete "English (US) — Master" seeded entry if it exists
        DB::table('base_languages')->where('name', 'English (US) — Master')->delete();
    }

    public function down(): void
    {
        if (Schema::hasColumn('base_languages', 'country_id')) {
            Schema::table('base_languages', function (Blueprint $table) {
                $table->dropColumn('country_id');
            });
        }
    }
};
