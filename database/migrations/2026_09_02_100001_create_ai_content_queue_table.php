<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_content_queue', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('batch_job_id')->nullable();
            $table->foreign('batch_job_id')->references('id')->on('ai_batch_jobs')->onDelete('set null');
            $table->enum('resource_type', ['business', 'category', 'feature'])->default('business');
            $table->unsignedBigInteger('resource_id');
            $table->string('content_field');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->string('openai_request_id')->nullable();
            $table->longText('result')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('prompt_id')->nullable();
            $table->timestamps();
            $table->index(['resource_type', 'resource_id']);
            $table->index(['batch_job_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_content_queue');
    }
};
