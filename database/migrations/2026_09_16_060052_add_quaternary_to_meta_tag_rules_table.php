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
        Schema::table('meta_tag_rules', function (Blueprint $table) {
            if (!Schema::hasColumn('meta_tag_rules', 'title_quaternary')) {
                $table->string('title_quaternary', 255)->nullable()->after('title_tertiary');
            }
            if (!Schema::hasColumn('meta_tag_rules', 'description_quaternary')) {
                $table->text('description_quaternary')->nullable()->after('description_tertiary');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meta_tag_rules', function (Blueprint $table) {
            if (Schema::hasColumn('meta_tag_rules', 'title_quaternary')) {
                $table->dropColumn('title_quaternary');
            }
            if (Schema::hasColumn('meta_tag_rules', 'description_quaternary')) {
                $table->dropColumn('description_quaternary');
            }
        });
    }
};
