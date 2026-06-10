<?php

namespace Database\Seeders;

use App\Models\MailTemplate;
use Illuminate\Database\Seeder;

class MailTemplateSeeder extends Seeder
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
                'body' => '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .otp { font-size: 24px; font-weight: bold; color: #007bff; padding: 10px; background: #f8f9fa; border-radius: 5px; text-align: center; margin: 20px 0; }
        h1 { color: #007bff; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Reset Your Password</h1>
        <p>We received a request to reset your password. Use the following OTP to proceed:</p>
        <div class="otp">{{ $otp }}</div>
        <p>If you did not request a password reset, please ignore this email.</p>
        <p>Thank you!</p>
    </div>
</body>
</html>',
                'variables' => ['otp'],
                'is_active' => true
            ],
            [
                'key' => 'welcome_email',
                'subject' => 'Welcome to {{ $app_name }}!',
                'body' => '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .welcome { background: #007bff; color: white; padding: 20px; border-radius: 5px; text-align: center; }
        h1 { margin: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="welcome">
            <h1>Welcome {{ $name }}!</h1>
        </div>
        <p>Thank you for joining {{ $app_name }}. We are excited to have you on board!</p>
        <p>Your account has been successfully created with the email: <strong>{{ $email }}</strong></p>
        <p>Best regards,<br>{{ $app_name }} Team</p>
    </div>
</body>
</html>',
                'variables' => ['name', 'email', 'app_name'],
                'is_active' => true
            ],
            [
                'key' => 'order_confirmation',
                'subject' => 'Order Confirmation #{{ $order_number }}',
                'body' => '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .order-info { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .total { font-size: 18px; font-weight: bold; color: #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Order Confirmation</h1>
        <p>Dear {{ $customer_name }},</p>
        <p>Thank you for your order! Here are the details:</p>

        <div class="order-info">
            <p><strong>Order Number:</strong> {{ $order_number }}</p>
            <p><strong>Order Date:</strong> {{ $order_date }}</p>
            <p><strong>Total Amount:</strong> <span class="total">${{ $total_amount }}</span></p>
        </div>

        <p>We will process your order shortly and send you a shipping confirmation.</p>
        <p>Thank you for choosing us!</p>
    </div>
</body>
</html>',
                'variables' => ['customer_name', 'order_number', 'order_date', 'total_amount'],
                'is_active' => true
            ]
        ];

        foreach ($templates as $template) {
            MailTemplate::create($template);
        }
    }
}
