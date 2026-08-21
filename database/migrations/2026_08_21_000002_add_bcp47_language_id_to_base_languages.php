<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Add bcp47_language_id column (nullable FK)
        Schema::table('base_languages', function (Blueprint $table) {
            $table->unsignedBigInteger('bcp47_language_id')->nullable()->after('id');
            $table->foreign('bcp47_language_id')->references('id')->on('bcp47_languages')->onDelete('set null');
        });

        // Step 2: Migrate existing data — match old 'code' values to bcp47_languages.code
        foreach (DB::table('base_languages')->get() as $bl) {
            $bcp47 = DB::table('bcp47_languages')->where('code', $bl->code)->first();
            if ($bcp47) {
                DB::table('base_languages')->where('id', $bl->id)->update(['bcp47_language_id' => $bcp47->id]);
            }
        }

        // Step 3: Drop old unique index and code column
        Schema::table('base_languages', function (Blueprint $table) {
            $table->dropUnique(['code']);
            $table->dropColumn('code');
        });
    }

    public function down(): void
    {
        // Restore code column
        Schema::table('base_languages', function (Blueprint $table) {
            $table->string('code')->nullable();
        });

        // Repopulate from bcp47_languages
        foreach (DB::table('base_languages')->get() as $bl) {
            if ($bl->bcp47_language_id) {
                $bcp47 = DB::table('bcp47_languages')->find($bl->bcp47_language_id);
                if ($bcp47) {
                    DB::table('base_languages')->where('id', $bl->id)->update(['code' => $bcp47->code]);
                }
            }
        }

        Schema::table('base_languages', function (Blueprint $table) {
            $table->dropForeign(['bcp47_language_id']);
            $table->dropColumn('bcp47_language_id');
        });
    }
};
