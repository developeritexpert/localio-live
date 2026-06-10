<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add `business_id` column to `users` table.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //  Add the business_id column (nullable, foreign key)
            $table->unsignedBigInteger('business_id')->nullable()->after('number');

            //  Add foreign key constraint
            $table->foreign('business_id')
                ->references('id')
                ->on('businesses')
                ->onDelete('set null'); // If business is deleted, set user.business_id to null
        });
    }

    /**
     * Remove `business_id` column from `users` table.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
            $table->dropColumn('business_id');
        });
    }
};
