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
        // 1. Extend businesses table
        Schema::table('businesses', function (Blueprint $table) {
            if (!Schema::hasColumn('businesses', 'is_claimed')) {
                $table->boolean('is_claimed')->default(0)->after('status');
            }
            if (!Schema::hasColumn('businesses', 'website')) {
                $table->string('website')->nullable()->after('is_claimed');
            }
        });

        // 2. business_claims
        if (!Schema::hasTable('business_claims')) {
            Schema::create('business_claims', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_id')->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('connection_type')->default('owner'); // owner, employee, representative, agency, other
                $table->boolean('agency_authorized')->default(0);
                $table->string('job_title')->nullable();
                $table->string('verification_method')->default('automatic'); // automatic, manual
                $table->string('work_email')->nullable()->index();
                $table->string('verification_code', 10)->nullable()->index();
                $table->string('verification_token', 64)->nullable()->unique();
                $table->timestamp('code_expires_at')->nullable();
                $table->text('evidence_text')->nullable();
                $table->string('evidence_document')->nullable();
                $table->string('evidence_channel')->nullable();
                $table->string('status')->default('pending_code'); // pending_code, pending_manual_review, approved, rejected, cancelled
                $table->text('admin_notes')->nullable();
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();

                $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 3. business_members
        if (!Schema::hasTable('business_members')) {
            Schema::create('business_members', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_id')->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('role')->default('owner'); // owner, admin, member
                $table->string('job_title')->nullable();
                $table->string('work_email')->nullable();
                $table->json('permissions')->nullable();
                $table->unsignedBigInteger('invited_by')->nullable();
                $table->string('status')->default('active'); // active, invited, suspended
                $table->string('invitation_token', 64)->nullable()->unique();
                $table->timestamps();

                $table->unique(['business_id', 'user_id']);
                $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 4. business_review_replies
        if (!Schema::hasTable('business_review_replies')) {
            Schema::create('business_review_replies', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('review_id')->index();
                $table->unsignedBigInteger('business_id')->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->text('reply_text');
                $table->string('status')->default('published'); // published, hidden
                $table->timestamps();

                $table->foreign('review_id')->references('id')->on('reviews')->onDelete('cascade');
                $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 5. business_review_invitations
        if (!Schema::hasTable('business_review_invitations')) {
            Schema::create('business_review_invitations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_id')->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('recipient_email')->index();
                $table->string('invitation_token', 64)->unique();
                $table->string('status')->default('sent'); // sent, opened, reviewed
                $table->timestamps();

                $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // 6. business_correction_requests
        if (!Schema::hasTable('business_correction_requests')) {
            Schema::create('business_correction_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('business_id')->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('issue_type')->default('general_info'); // general_info, pricing, features, contact, other
                $table->text('current_info')->nullable();
                $table->text('suggested_correction');
                $table->string('supporting_link')->nullable();
                $table->string('status')->default('pending'); // pending, applied, declined
                $table->text('admin_notes')->nullable();
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->timestamps();

                $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_correction_requests');
        Schema::dropIfExists('business_review_invitations');
        Schema::dropIfExists('business_review_replies');
        Schema::dropIfExists('business_members');
        Schema::dropIfExists('business_claims');

        Schema::table('businesses', function (Blueprint $table) {
            if (Schema::hasColumn('businesses', 'website')) {
                $table->dropColumn('website');
            }
            if (Schema::hasColumn('businesses', 'is_claimed')) {
                $table->dropColumn('is_claimed');
            }
        });
    }
};

