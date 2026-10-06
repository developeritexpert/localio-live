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
        Schema::table('business_pro_cons', function (Blueprint $table) {
            $table->foreignId('category_pro_con_id')
                ->nullable()
                ->after('business_id')
                ->constrained('category_pro_cons')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_pro_cons', function (Blueprint $table) {
            $table->dropForeign(['category_pro_con_id']);
            $table->dropColumn('category_pro_con_id');
        });
    }
};