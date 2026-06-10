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
        Schema::table('languages', function (Blueprint $table) {
            $table->unsignedBigInteger('base_language_id')->nullable()->after('id');
            $table->foreign('base_language_id')
                  ->references('id')
                  ->on('languages')
                  ->onDelete('set null');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('languages', function (Blueprint $table) {
            $table->dropForeign(['base_language_id']);
            $table->dropColumn('base_language_id');
        });
    }
};
