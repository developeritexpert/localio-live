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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Schema::dropIfExists('business');  
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('meta_title', 191)->nullable();
            $table->string('meta_description', 191)->nullable();
            $table->string('affiliate_partner', 191)->nullable();
            $table->string('affiliate_link', 191)->nullable();
            $table->tinyInteger('active_all_countries')->default(1);
            $table->string('permanent_url', 191)->nullable();
            $table->string('icon_id', 191)->nullable();
            $table->string('image_id', 191)->nullable();
            $table->string('year_found', 191)->nullable();
            $table->string('languages_supported', 191)->nullable();
            $table->string('created_by', 191)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
                // Foreign key constraint
    $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

        });
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');   

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        Schema::dropIfExists('business');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');   
    }
};
