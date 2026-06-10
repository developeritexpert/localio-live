<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('business_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('field')->nullable();;        // e.g., name, icon_id
            $table->string('type')->nullable();;         // business, translation, review_translation, etc.
            $table->string('column')->nullable();;       // actual column in DB
            $table->text('value')->nullable();;          // new value
            $table->unsignedBigInteger('lang_id')->nullable(); // null if not translation-based
            $table->unsignedBigInteger('requested_by');
            $table->string('action_type')->nullable();  
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');       

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_change_requests');
    }
};
