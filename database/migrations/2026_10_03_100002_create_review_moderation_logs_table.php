<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_moderation_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_id');

            $table->string('action');
            // status_changed | ai_layer1_check | ai_layer2_check | ai_layer3_result
            // moderated | appealed | reported | re_moderated

            $table->string('from_status')->nullable();
            $table->string('to_status')->nullable();

            $table->string('actor_type');
            // system | moderator | user

            $table->unsignedBigInteger('actor_id')->nullable();
            // user/admin ID if actor_type is moderator or user

            $table->string('layer')->nullable();
            // layer1 | layer2 | layer3 | human

            $table->json('metadata')->nullable();
            // Arbitrary JSON: AI flags, categories, reason, batch_id, etc.

            $table->timestamps();

            $table->foreign('review_id')
                  ->references('id')
                  ->on('reviews')
                  ->onDelete('cascade');

            $table->index('review_id');
            $table->index(['actor_type', 'actor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_moderation_logs');
    }
};
