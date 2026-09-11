<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('business_discussions')) { 
            Schema::create('business_discussions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('author_name')->nullable();
                $table->string('author_title')->nullable();
                $table->string('author_avatar')->nullable();
                $table->string('category')->default('General');
                $table->string('title');
                $table->text('content');
                $table->unsignedInteger('upvotes_count')->default(0);
                $table->unsignedInteger('downvotes_count')->default(0);
                $table->unsignedInteger('comments_count')->default(0);
                $table->unsignedInteger('views_count')->default(0);
                $table->boolean('is_pinned')->default(false);
                $table->string('status')->default('active');
                $table->timestamps();

                $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('business_discussion_comments')) {
            Schema::create('business_discussion_comments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_discussion_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->unsignedBigInteger('parent_id')->nullable()->index();
                $table->string('author_name')->nullable();
                $table->string('author_title')->nullable();
                $table->string('author_avatar')->nullable();
                $table->text('content');
                $table->unsignedInteger('upvotes_count')->default(0);
                $table->unsignedInteger('downvotes_count')->default(0);
                $table->string('status')->default('active');
                $table->timestamps();

                $table->foreign('business_discussion_id')->references('id')->on('business_discussions')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('parent_id')->references('id')->on('business_discussion_comments')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('business_discussion_votes')) {
            Schema::create('business_discussion_votes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('ip_address')->nullable();
                $table->string('voteable_type');
                $table->unsignedBigInteger('voteable_id');
                $table->string('vote_type')->default('up');
                $table->timestamps();

                $table->index(['voteable_type', 'voteable_id']);
            });
        }

        if (Schema::hasTable('languages') && !Schema::hasColumn('languages', 'discussions_slug')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->string('discussions_slug')->nullable()->default('discussions')->after('comparisons_slug');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_discussion_votes');
        Schema::dropIfExists('business_discussion_comments');
        Schema::dropIfExists('business_discussions');

        if (Schema::hasTable('languages') && Schema::hasColumn('languages', 'discussions_slug')) {
            Schema::table('languages', function (Blueprint $table) {
                $table->dropColumn('discussions_slug');
            });
        }
    }
};
