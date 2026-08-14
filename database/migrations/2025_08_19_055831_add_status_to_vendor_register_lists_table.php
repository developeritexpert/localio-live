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
        if (!Schema::hasColumn('vendor_register_lists', 'status')) {
            Schema::table('vendor_register_lists', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('business_id');
              // status: pending, approve, reject
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('vendor_register_lists', 'status')) {
            Schema::table('vendor_register_lists', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        }
    }
};
