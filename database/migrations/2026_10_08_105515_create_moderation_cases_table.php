<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moderation_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_number', 50)->unique();
            $table->string('title');
            $table->string('type', 50)->default('coordinated_manipulation');
            $table->unsignedBigInteger('business_id')->nullable();
            $table->string('status', 30)->default('open'); // open, investigating, resolved, dismissed
            $table->text('summary')->nullable();
            $table->json('signals')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');

            $table->index('status');
            $table->index('type');
            $table->index('business_id');
        });

        Schema::create('moderation_case_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('case_id');
            $table->unsignedBigInteger('review_id');
            $table->timestamps();

            $table->foreign('case_id')->references('id')->on('moderation_cases')->onDelete('cascade');
            $table->foreign('review_id')->references('id')->on('reviews')->onDelete('cascade');

            $table->unique(['case_id', 'review_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moderation_case_reviews');
        Schema::dropIfExists('moderation_cases');
    }
};