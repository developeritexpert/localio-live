<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\MailTemplate;
use App\Models\MailTemplateTranslation;
use App\Models\Language;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $templates = [
            [
                'key' => 'business_claim_code',
                'subject' => 'Your Localio verification code: {verification_code}',
                'body' => '<p>Hello {user_name},</p><p>You requested to claim the business profile for <strong>{business_name}</strong> on Localio.</p><p>Your verification code is: <strong style="font-size: 24px; letter-spacing: 2px; color: #003f7d;">{verification_code}</strong></p><p>Or you can verify instantly by clicking below:</p><p><a href="{verification_url}" style="display:inline-block;padding:10px 20px;background:#003f7d;color:#fff;border-radius:4px;text-decoration:none;">Verify Profile Claim</a></p><p>This code expires in 24 hours.</p>',
                'variables' => json_encode(['user_name', 'business_name', 'verification_code', 'verification_url']),
                'is_active' => true,
            ],
            [
                'key' => 'business_claim_approved',
                'subject' => 'Your claim for {business_name} has been approved!',
                'body' => '<p>Hello {user_name},</p><p>Great news! Your request to manage <strong>{business_name}</strong> on Localio has been approved.</p><p>You can now manage your business profile, respond to customer reviews, and invite team members directly from your Localio dashboard.</p><p><a href="{dashboard_url}" style="display:inline-block;padding:10px 20px;background:#003f7d;color:#fff;border-radius:4px;text-decoration:none;">Go to Business Dashboard</a></p>',
                'variables' => json_encode(['user_name', 'business_name', 'dashboard_url']),
                'is_active' => true,
            ],
            [
                'key' => 'business_claim_rejected',
                'subject' => 'Update regarding your claim for {business_name}',
                'body' => '<p>Hello {user_name},</p><p>Thank you for submitting a claim for <strong>{business_name}</strong> on Localio.</p><p>After reviewing the provided evidence, we were unable to approve your claim at this time.</p><p><strong>Reason:</strong> {reason}</p><p>If you have additional documentation, feel free to submit a new claim request or contact our support team.</p>',
                'variables' => json_encode(['user_name', 'business_name', 'reason']),
                'is_active' => true,
            ],
            [
                'key' => 'business_team_invitation',
                'subject' => "You've been invited to manage {business_name} on Localio",
                'body' => '<p>Hello,</p><p>{inviter_name} has invited you to join the team managing <strong>{business_name}</strong> on Localio as a <strong>{role}</strong>.</p><p><a href="{invitation_url}" style="display:inline-block;padding:10px 20px;background:#003f7d;color:#fff;border-radius:4px;text-decoration:none;">Accept Team Invitation</a></p>',
                'variables' => json_encode(['inviter_name', 'business_name', 'role', 'invitation_url']),
                'is_active' => true,
            ],
            [
                'key' => 'business_review_invitation',
                'subject' => '{business_name} would love to hear your feedback on Localio',
                'body' => '<p>Hello,</p><p><strong>{business_name}</strong> invites you to share your genuine experience and leave a review on Localio.</p><p>Your honest feedback helps the community make informed decisions.</p><p><a href="{review_url}" style="display:inline-block;padding:10px 20px;background:#003f7d;color:#fff;border-radius:4px;text-decoration:none;">Write a Review</a></p>',
                'variables' => json_encode(['business_name', 'review_url']),
                'is_active' => true,
            ],
        ];

        $languages = Language::all();

        foreach ($templates as $tData) {
            $template = MailTemplate::updateOrCreate(
                ['key' => $tData['key']],
                [
                    'subject'   => $tData['subject'],
                    'body'      => $tData['body'],
                    'variables' => $tData['variables'],
                    'is_active' => $tData['is_active'],
                ]
            );

            foreach ($languages as $lang) {
                MailTemplateTranslation::updateOrCreate(
                    [
                        'mail_template_id' => $template->id,
                        'lang_id'          => $lang->id,
                    ],
                    [
                        'subject' => $tData['subject'],
                        'body'    => $tData['body'],
                    ]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $keys = [
            'business_claim_code',
            'business_claim_approved',
            'business_claim_rejected',
            'business_team_invitation',
            'business_review_invitation',
        ];

        $templates = MailTemplate::whereIn('key', $keys)->get();
        foreach ($templates as $t) {
            MailTemplateTranslation::where('mail_template_id', $t->id)->delete();
            $t->delete();
        }
    }
};

