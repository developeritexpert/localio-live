<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $subject ?? config('app.name', 'Localio') }}</title>
    <style type="text/css">
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        body { margin: 0; padding: 0; width: 100% !important; min-width: 100%; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f1f5f9; padding: 40px 0 30px 0; }
        .main-container { width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; }
        .header { background-color: #ffffff; padding: 28px 40px 20px 40px; text-align: left; border-bottom: 1px solid #f1f5f9; }
        .header img { max-height: 38px; width: auto; display: block; }
        .content { padding: 36px 40px 40px 40px; color: #1e3050; font-size: 15px; line-height: 1.6; }
        .content h1, .content h2, .content h3 { color: #002347; font-weight: 700; margin-top: 0; margin-bottom: 18px; line-height: 1.3; }
        .content h1 { font-size: 24px; }
        .content h2 { font-size: 20px; }
        .content p { margin: 0 0 16px 0; color: #334155; font-size: 15px; line-height: 1.65; }
        .content ul, .content ol { margin: 0 0 16px 0; padding-left: 24px; color: #334155; }
        .content li { margin-bottom: 6px; }
        .btn-primary { display: inline-block; background-color: #007bff; color: #ffffff !important; font-weight: 600; font-size: 15px; text-decoration: none; padding: 13px 28px; border-radius: 8px; margin: 18px 0; text-align: center; }
        .btn-primary:hover { background-color: #0056b3; }
        .btn-orange { display: inline-block; background-color: #ff5722; color: #ffffff !important; font-weight: 600; font-size: 15px; text-decoration: none; padding: 13px 28px; border-radius: 8px; margin: 18px 0; text-align: center; }
        .otp-box { background-color: #f8fafc; border: 2px dashed #007bff; border-radius: 10px; padding: 18px 24px; text-align: center; margin: 24px 0; }
        .otp-code { font-size: 32px; font-weight: 800; letter-spacing: 6px; color: #002347; margin: 0; font-family: 'Courier New', monospace; }
        .info-card { background-color: #f8fafc; border-left: 4px solid #007bff; border-radius: 6px; padding: 14px 18px; margin: 20px 0; }
        .info-card p { margin: 0; font-size: 14px; color: #475569; }
        .footer { background-color: #f8fafc; padding: 24px 40px; text-align: center; font-size: 13px; color: #64748b; border-top: 1px solid #e2e8f0; }
        .footer a { color: #007bff; text-decoration: none; }
        .footer a:hover { text-decoration: underline; }
        @media only screen and (max-width: 620px) {
            .wrapper { padding: 15px 10px; }
            .content { padding: 25px 20px; }
            .header { padding: 20px; }
            .footer { padding: 20px; }
            .otp-code { font-size: 26px; letter-spacing: 4px; }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td align="center">
                    <div class="main-container">
                        <!-- Header -->
                        <div class="header">
                            <a href="{{ url('/') }}" target="_blank" style="text-decoration: none; display: inline-block;">
                                <img src="{{ asset('front/img/logo.png') }}" alt="{{ config('app.name', 'Localio') }}" />
                            </a>
                        </div>

                        <!-- Content -->
                        <div class="content">
                            {!! $content !!}
                        </div>

                        <!-- Footer -->
                        <div class="footer">
                            <p style="margin: 0 0 8px 0; font-size: 13px; color: #64748b;">
                                &copy; {{ date('Y') }} {{ config('app.name', 'Localio') }}. All rights reserved.
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                                <a href="{{ url('/') }}" target="_blank">{{ config('app.name', 'Localio') }}</a>
                                &nbsp;&bull;&nbsp;
                                <a href="{{ url('/' . (app()->getLocale() ?? 'en-us') . '/contact') }}" target="_blank">Contact Support</a>
                                @if(isset($showPreferences) && $showPreferences)
                                    &nbsp;&bull;&nbsp;
                                    <a href="{{ url('/' . (app()->getLocale() ?? 'en-us') . '/user-dashboard/email-preferences') }}" target="_blank">Email Preferences</a>
                                @endif
                            </p>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
