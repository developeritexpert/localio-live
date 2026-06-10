<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('expert_guide_articles');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        Schema::create('expert_guide_articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id'); // FK to expert_guide_categories
            $table->string('image')->nullable();
            $table->boolean('status')->default(1); // 1 = active, 0 = inactive
            $table->timestamps();

            $table->foreign('category_id')
                ->references('id')
                ->on('expert_guide_categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expert_guide_articles');
    }
};
