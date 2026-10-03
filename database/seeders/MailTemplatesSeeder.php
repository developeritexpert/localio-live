<?php

namespace Database\Seeders;

use App\Models\MailTemplate;
use Illuminate\Database\Seeder;

class MailTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'key' => 'forget_password',
                'subject' => 'Reset Your Password',
                'variables' => ['otp', 'name', 'app_name'],
                'is_active' => true,
                'body' => '<h2>Reset Your Password</h2>
<p>Hello {{ $name }},</p>
<p>We received a request to reset your password for your {{ $app_name }} account. Use the verification code below to proceed:</p>
<div class="otp-box">
    <p class="otp-code">{{ $otp }}</p>
</div>
<div class="info-card">
    <p>This code will expire in 15 minutes. For your security, never share this code with anyone.</p>
</div>
<p>If you did not request a password reset, you can safely ignore this email.</p>
<p>Best regards,<br>The {{ $app_name }} Team</p>',
            ],
            [
                'key' => 'welcome_email',
                'subject' => 'Welcome to {{ $app_name }}!',
                'variables' => ['name', 'email', 'app_name', 'login_url'],
                'is_active' => true,
                'body' => '<h2>Welcome to {{ $app_name }}, {{ $name }}!</h2>
<p>We are excited to have you join our community of smart consumers and business owners.</p>
<p>With your {{ $app_name }} account, you can:</p>
<ul>
    <li><strong>Discover & Compare:</strong> Find trusted alternatives and authentic reviews for products and services.</li>
    <li><strong>Share Your Experience:</strong> Write reviews to help others make informed buying decisions.</li>
    <li><strong>Join Discussions:</strong> Ask questions, share insights, and connect with fellow users.</li>
</ul>
<p style="text-align: center; margin: 30px 0;">
    <a href="{{ $login_url }}" class="btn-primary">Go to Your Account</a>
</p>
<p>If you have any questions or feedback, feel free to contact our support team at any time.</p>
<p>Warm regards,<br>The {{ $app_name }} Team</p>',
            ],
            [
                'key' => 'password_changed',
                'subject' => 'Your password has been changed',
                'variables' => ['name', 'app_name', 'support_url'],
                'is_active' => true,
                'body' => '<h2>Password Updated Successfully</h2>
<p>Hello {{ $name }},</p>
<p>This is a quick security confirmation that the password for your {{ $app_name }} account was recently changed.</p>
<div class="info-card">
    <p><strong>Didn\'t make this change?</strong> If you did not request this update, your account may be compromised. Please contact support immediately.</p>
</div>
<p style="text-align: center; margin: 25px 0;">
    <a href="{{ $support_url }}" class="btn-primary">Contact Support</a>
</p>
<p>Thank you,<br>The {{ $app_name }} Security Team</p>',
            ],
            [
                'key' => 'review_submitted_user',
                'subject' => 'Your review for {{ $business_name }} has been received',
                'variables' => ['user_name', 'business_name', 'rating', 'app_name'],
                'is_active' => true,
                'body' => '<h2>Thank you for your review, {{ $user_name }}!</h2>
<p>We have successfully received your {{ $rating }}-star review for <strong>{{ $business_name }}</strong>.</p>
<div class="info-card">
    <p>Our moderation team reviews all submissions to ensure authenticity and quality. Your review will be published as soon as verification is complete.</p>
</div>
<p>Your feedback helps thousands of consumers and business buyers make better decisions every day.</p>
<p>Best regards,<br>The {{ $app_name }} Team</p>',
            ],
            [
                'key' => 'review_submitted_admin',
                'subject' => 'New Review Pending Moderation: {{ $business_name }}',
                'variables' => ['user_name', 'business_name', 'rating', 'admin_review_url'],
                'is_active' => true,
                'body' => '<h2>New Review Submitted</h2>
<p>A new review has been submitted and is currently pending moderation:</p>
<ul>
    <li><strong>Business:</strong> {{ $business_name }}</li>
    <li><strong>Reviewer:</strong> {{ $user_name }}</li>
    <li><strong>Rating:</strong> {{ $rating }} / 5 stars</li>
</ul>
<p style="text-align: center; margin: 25px 0;">
    <a href="{{ $admin_review_url }}" class="btn-primary">Review & Moderate</a>
</p>',
            ],
            [
                'key' => 'review_approved',
                'subject' => 'Your review for {{ $business_name }} is now live!',
                'variables' => ['user_name', 'business_name', 'review_url', 'app_name'],
                'is_active' => true,
                'body' => '<h2>Your Review is Live, {{ $user_name }}!</h2>
<p>Great news! Your review for <strong>{{ $business_name }}</strong> has been verified and published on {{ $app_name }}.</p>
<p>You can view your published review using the link below:</p>
<p style="text-align: center; margin: 25px 0;">
    <a href="{{ $review_url }}" class="btn-primary">View Your Review</a>
</p>
<p>Thank you for contributing to the community!</p>
<p>Warm regards,<br>The {{ $app_name }} Team</p>',
            ],
            [
                'key' => 'review_rejected',
                'subject' => 'Update regarding your review for {{ $business_name }}',
                'variables' => ['user_name', 'business_name', 'reason', 'app_name'],
                'is_active' => true,
                'body' => '<h2>Update on Your Review Submission</h2>
<p>Hello {{ $user_name }},</p>
<p>Thank you for your review submission for <strong>{{ $business_name }}</strong> on {{ $app_name }}.</p>
<p>During our routine review moderation, our team was unable to publish your review for the following reason:</p>
<div class="info-card">
    <p><strong>Reason:</strong> {{ $reason }}</p>
</div>
<p>You are welcome to revise your review according to our community guidelines and resubmit it.</p>
<p>Best regards,<br>The {{ $app_name }} Team</p>',
            ],
            [
                'key' => 'business_new_review',
                'subject' => 'New {{ $rating }}-star review received for {{ $business_name }}',
                'variables' => ['business_name', 'reviewer_name', 'rating', 'review_url', 'app_name'],
                'is_active' => true,
                'body' => '<h2>New Customer Review Received!</h2>
<p>Hello,</p>
<p>A new review has just been submitted for <strong>{{ $business_name }}</strong> on {{ $app_name }}:</p>
<div class="info-card">
    <p><strong>Rating:</strong> {{ $rating }} / 5 stars</p>
    <p><strong>Reviewer:</strong> {{ $reviewer_name }}</p>
</div>
<p style="text-align: center; margin: 25px 0;">
    <a href="{{ $review_url }}" class="btn-primary">Read & Respond</a>
</p>
<p>Promptly engaging with customer reviews helps build lasting credibility and trust.</p>
<p>Best regards,<br>The {{ $app_name }} Team</p>',
            ],
            [
                'key' => 'contact_us_user',
                'subject' => 'We have received your message - {{ $subject }}',
                'variables' => ['name', 'subject', 'app_name'],
                'is_active' => true,
                'body' => '<h2>Message Received</h2>
<p>Hello {{ $name }},</p>
<p>Thank you for reaching out to {{ $app_name }}. We have received your inquiry regarding <strong>{{ $subject }}</strong>.</p>
<p>Our team is reviewing your message and will get back to you as soon as possible, typically within 1 business day.</p>
<p>Best regards,<br>The {{ $app_name }} Support Team</p>',
            ],
            [
                'key' => 'contact_us_admin',
                'subject' => 'New Contact Inquiry: {{ $subject }}',
                'variables' => ['name', 'email', 'subject', 'message', 'admin_query_url'],
                'is_active' => true,
                'body' => '<h2>New Contact Inquiry Submitted</h2>
<p>A new user inquiry has been submitted through the contact form:</p>
<ul>
    <li><strong>From:</strong> {{ $name }} ({{ $email }})</li>
    <li><strong>Subject:</strong> {{ $subject }}</li>
</ul>
<div class="info-card">
    <p><strong>Message:</strong></p>
    <p>{{ $message }}</p>
</div>
<p style="text-align: center; margin: 25px 0;">
    <a href="{{ $admin_query_url }}" class="btn-primary">View in Admin Panel</a>
</p>',
            ],
            [
                'key' => 'query_reply',
                'subject' => 'Re: {{ $subject }}',
                'variables' => ['name', 'subject', 'reply_message', 'app_name'],
                'is_active' => true,
                'body' => '<h2>Response from {{ $app_name }} Support</h2>
<p>Hello {{ $name }},</p>
<p>Regarding your message about <em>"{{ $subject }}"</em>, our support team has responded:</p>
<div class="info-card">
    <p>{!! nl2br(e($reply_message)) !!}</p>
</div>
<p>If you have any further questions, feel free to reply directly to this email.</p>
<p>Best regards,<br>The {{ $app_name }} Support Team</p>',
            ],
            [
                'key' => 'vendor_registration_user',
                'subject' => 'Your vendor application for {{ $business_name }} has been received',
                'variables' => ['vendor_name', 'business_name', 'app_name'],
                'is_active' => true,
                'body' => '<h2>Vendor Application Received</h2>
<p>Hello {{ $vendor_name }},</p>
<p>Thank you for registering to claim and manage <strong>{{ $business_name }}</strong> on {{ $app_name }}.</p>
<div class="info-card">
    <p>Our team is reviewing your application and verifying business details. Once approved, you will have full access to your vendor dashboard to customize your profile, view leads, and reply to reviews.</p>
</div>
<p>We typically review vendor applications within 24 to 48 hours.</p>
<p>Warm regards,<br>The {{ $app_name }} Partnerships Team</p>',
            ],
            [
                'key' => 'vendor_registration_admin',
                'subject' => 'New Vendor Registration Request: {{ $business_name }}',
                'variables' => ['vendor_name', 'business_name', 'email', 'admin_vendor_url'],
                'is_active' => true,
                'body' => '<h2>New Vendor Registration Request</h2>
<p>A new vendor account request has been submitted:</p>
<ul>
    <li><strong>Vendor:</strong> {{ $vendor_name }}</li>
    <li><strong>Email:</strong> {{ $email }}</li>
    <li><strong>Business:</strong> {{ $business_name }}</li>
</ul>
<p style="text-align: center; margin: 25px 0;">
    <a href="{{ $admin_vendor_url }}" class="btn-primary">Review Vendor Requests</a>
</p>',
            ],
            [
                'key' => 'order_confirmation',
                'subject' => 'Order Confirmation #{{ $order_number }}',
                'variables' => ['customer_name', 'order_number', 'order_date', 'total_amount', 'app_name'],
                'is_active' => true,
                'body' => '<h2>Order Confirmation</h2>
<p>Dear {{ $customer_name }},</p>
<p>Thank you for your order! Here are your order details:</p>
<div class="info-card">
    <p><strong>Order Number:</strong> #{{ $order_number }}</p>
    <p><strong>Order Date:</strong> {{ $order_date }}</p>
    <p><strong>Total Amount:</strong> {{ $total_amount }}</p>
</div>
<p>We are processing your order and will keep you updated on its status.</p>
<p>Best regards,<br>The {{ $app_name }} Team</p>',
            ],
            [
                'key' => 'vendor_status_update',
                'subject' => 'Your vendor account status for {{ $business_name }}',
                'variables' => ['vendor_name', 'business_name', 'status', 'dashboard_url', 'app_name'],
                'is_active' => true,
                'body' => '<h2>Vendor Account Status Update</h2>
<p>Hello {{ $vendor_name }},</p>
<p>We are writing to update you on your vendor registration for <strong>{{ $business_name }}</strong> on {{ $app_name }}.</p>
<div class="info-card">
    <p><strong>Application Status:</strong> {{ $status }}</p>
</div>
<p>If your application has been approved, you may now log in to access your vendor dashboard:</p>
<p style="text-align: center; margin: 25px 0;">
    <a href="{{ $dashboard_url }}" class="btn-primary">Go to Vendor Dashboard</a>
</p>
<p>Best regards,<br>The {{ $app_name }} Team</p>',
            ],
        ];

        foreach ($templates as $t) {
            MailTemplate::updateOrCreate(
                ['key' => $t['key']],
                [
                    'subject' => $t['subject'],
                    'body' => $t['body'],
                    'variables' => $t['variables'],
                    'is_active' => $t['is_active'],
                ]
            );
        }
    }
}
