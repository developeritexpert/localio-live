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
    Schema::table('help_center_contents', function (Blueprint $table) {
        $table->string('knowledge_base_title')->nullable()->after('meta_description');
        $table->text('knowledge_base_description')->nullable()->after('knowledge_base_title');
        $table->string('knowledge_base_image')->nullable()->after('knowledge_base_description');

        $table->string('help_center_title')->nullable()->after('knowledge_base_image');
        $table->text('help_center_description')->nullable()->after('help_center_title');
        $table->string('help_center_image')->nullable()->after('help_center_description');
    });
}

public function down(): void
{
    Schema::table('help_center_contents', function (Blueprint $table) {
        $table->dropColumn([
            'knowledge_base_title',
            'knowledge_base_description',
            'knowledge_base_image',
            'help_center_title',
            'help_center_description',
            'help_center_image',
        ]);
    });
}

};
