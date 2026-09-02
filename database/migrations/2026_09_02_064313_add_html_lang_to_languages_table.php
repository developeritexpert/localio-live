<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('languages') && !Schema::hasColumn('languages', 'html_lang')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->string('html_lang', 20)->nullable()->after('lang_code');
            });
        }

        // Default seed for existing languages if html_lang is empty
        DB::table('languages')->where('lang_code', 'en-us')->update(['html_lang' => 'en-US']);
    }

    public function down(): void
    {
        if (Schema::hasTable('languages') && Schema::hasColumn('languages', 'html_lang')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->dropColumn('html_lang');
            });
        }
    }
};