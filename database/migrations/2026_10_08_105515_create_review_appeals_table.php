<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_appeals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_id');
            $table->unsignedBigInteger('user_id');
            $table->text('appeal_reason');
            $table->string('status', 30)->default('open'); // open, under_review, resolved
            $table->unsignedBigInteger('moderator_id')->nullable();
            $table->string('moderator_decision', 50)->nullable(); // restored, confirmed_rejection, info_requested
            $table->text('moderator_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->foreign('review_id')->references('id')->on('reviews')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('moderator_id')->references('id')->on('users')->onDelete('set null');

            $table->index('review_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_appeals');
    }
};