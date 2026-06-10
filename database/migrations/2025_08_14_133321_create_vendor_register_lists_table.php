<?php

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
//     public function up(): void
//     {
//         Schema::create('vendor_register_lists', function (Blueprint $table) {
//             $table->id();
//             $table->string('first_name');
//             $table->string('last_name');
//             $table->string('email')->unique();
//             $table->string('password');
//             $table->unsignedBigInteger('business_id');
//             $table->timestamps();
//             $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('vendor_register_lists');
//     }
// };
