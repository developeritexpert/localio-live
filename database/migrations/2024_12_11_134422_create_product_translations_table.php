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
       Schema::create('product_translations', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('product_id');
    $table->unsignedBigInteger('lang_id');
    $table->string('product_link')->nullable();
    $table->string('name');
    $table->string('slug');
    $table->string('location')->nullable();
    $table->string('address')->nullable();
    $table->string('language_supported')->nullable();
    $table->longText('description')->nullable();
    $table->longText('overview')->nullable();
    $table->string('status')->nullable()->default('active');
    $table->timestamps();
    $table->foreign('product_id')->references('id')->on('products');
    $table->foreign('lang_id')->references('id')->on('languages');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('product_translations');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
