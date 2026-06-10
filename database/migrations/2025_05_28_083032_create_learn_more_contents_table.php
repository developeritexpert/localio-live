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
        Schema::create('learn_more_contents', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('section_type')->default('body'); 
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->unsignedBigInteger('lang_id');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('learn_more_contents');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
