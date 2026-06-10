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
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('lang_code', 10)->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('set null');
            $table->integer('status')->default(1);
            $table->tinyInteger('is_active_translation')->default(0);
            $table->tinyInteger('is_valid_language_code')->default(0);
            $table->timestamps();
            });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Schema::dropIfExists('languages');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
};
