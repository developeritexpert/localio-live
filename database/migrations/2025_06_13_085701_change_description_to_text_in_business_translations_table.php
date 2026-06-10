<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('business_translations', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('business_translations', function (Blueprint $table) {
            $table->string('description', 255)->nullable()->change();
        });
    }
    
};
