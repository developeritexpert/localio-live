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
        Schema::create('filters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained(table: 'categories')->onDelete('cascade');
            $table->foreignId('filter_type_id')->constrained('filter_types')->onDelete('cascade');;
            $table->string('name');
            $table->string('slug');
            $table->boolean('is_required')->default(false);
            $table->integer('display_order')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Schema::dropIfExists('filters');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
};
