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
        Schema::table('category_translations', function (Blueprint $table) {
            $table->text('worth_it_content')->nullable()->after('description');
            $table->text('best_for_content')->nullable()->after('worth_it_content');
            $table->text('integrations_content')->nullable()->after('best_for_content');
            $table->text('security_compliance_content')->nullable()->after('integrations_content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_translations', function (Blueprint $table) {
            $table->dropColumn([
                'worth_it_content',
                'best_for_content',
                'integrations_content',
                'security_compliance_content',
              
            ]);
        });
    }
};
