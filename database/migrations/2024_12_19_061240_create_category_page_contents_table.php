<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category_page_contents', function (Blueprint $table) {
            $table->id();
            $table->string('meta_key');
            $table->text('meta_value');
            $table->string('type')->default('text');
            $table->integer('lang_id')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Schema::dropIfExists('category_page_contents');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
};
