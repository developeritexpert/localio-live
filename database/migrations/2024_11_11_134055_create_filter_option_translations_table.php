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

        Schema::create('filter_option_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filter_option_id')->constrained('filter_options')->onDelete('cascade');
            $table->foreignId('language_id')->nullable()->constrained('languages')->onDelete('set null');
            $table->string('name')->nullable();
            $table->decimal('min_value', 10, 2)->nullable();
            $table->decimal('max_value', 10, 2)->nullable();
            $table->string('unit')->nullable();
            $table->decimal('default_toggle', 10, 2)->nullable();
            $table->decimal('default_range', 10, 2)->nullable();
            $table->string('on_label')->nullable();
            $table->string('off_label')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Schema::dropIfExists('filter_option_translations');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
};
