<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pricing_options', 'scope')) {
            Schema::table('pricing_options', function (Blueprint $table) {
                $table->enum('scope', ['global', 'category_specific'])->default('global')->after('slug');
            });
        }

        if (!Schema::hasColumn('pricing_option_translations', 'button_text')) {
            Schema::table('pricing_option_translations', function (Blueprint $table) {
                $table->string('button_text')->nullable()->after('name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pricing_options', 'scope')) {
            Schema::table('pricing_options', function (Blueprint $table) {
                $table->dropColumn('scope');
            });
        }

        if (Schema::hasColumn('pricing_option_translations', 'button_text')) {
            Schema::table('pricing_option_translations', function (Blueprint $table) {
                $table->dropColumn('button_text');
            });
        }
    }
};
