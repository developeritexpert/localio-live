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
        Schema::create('expert_guides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lang_id')->default(1);
            $table->string('title');
            $table->text('description');
            $table->string('education_title');
            $table->text('education_description');
            $table->string('smart_search');
            $table->text('smart_search_description');
            $table->text('how_to_check_email'); // CKEditor field
            $table->string('overview');
            $table->LongText('email_description');
            $table->string('webmail');
            $table->LongText('webmail_description')->nullable();
            $table->string('email_application');
            $table->LongText('email_app_description')->nullable();
            $table->string('imap');
            $table->LongText('imap_pop')->nullable();
            $table->string('right_tool_heading')->nullable();
            $table->string('get_start_button')->nullable();
            $table->string('meta_title');
            $table->text('meta_description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expert_guides');
    }
};
