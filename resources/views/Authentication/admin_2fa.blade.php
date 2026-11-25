@extends('user_layout.master')
@section('meta_title', 'Admin Verification - Localio')
@section('content')
<section class="banner_sec help-cntr-bnrs inr-bnr dark lg_Bnr" style="padding-bottom: 100px !important; background-color:#f7f9fb">
    <!-- <div class="bubble-wrp">
        <img src="{{ asset('front/img/small-bnnr-bg.png') }}" alt="">
    </div> -->
</section>

<section class="contact_sec login_form p_120 pt-0 light" style=" background-color:#f7f9fb">
    <div class="container">
        <div class="contact_content" data-aos="fade-up" data-aos-duration="1000">
            <div class="hd_text">
                <div style="width: 56px; height: 56px; margin: 0 auto 16px; border-radius: 50%; background: #e0f2fe; color: #003f7d; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <h2 class="text-center">Two-Factor Authentication</h2>
                @php
                    $adminEmail = session('admin_2fa_email', '');
                    $maskedEmail = '';
                    if ($adminEmail && strpos($adminEmail, '@') !== false) {
                        [$name, $domain] = explode('@', $adminEmail);
                        $maskedEmail = substr($name, 0, 2) . str_repeat('*', max(1, strlen($name) - 2)) . '@' . $domain;
                    }
                @endphp
                <p class="text-center">
                    To protect administrative access, a 6-digit verification code was sent to your registered account email
                    @if($maskedEmail)
                        <strong>({{ $maskedEmail }})</strong>
                    @endif.
                </p>
            </div>

            @if (session('success'))
                <div class="alert alert-success py-2 px-3 mb-3 text-center" style="font-size: 14px; border-radius: 8px;">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger py-2 px-3 mb-3 text-center" style="font-size: 14px; border-radius: 8px;">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->has('otp'))
                <div class="alert alert-danger py-2 px-3 mb-3 text-center" style="font-size: 14px; border-radius: 8px;">
                    {{ $errors->first('otp') }}
                </div>
            @endif

            <form class="login_form" action="{{ route('admin.2fa.verify') }}" method="POST" id="admin2faForm" autocomplete="off">
                @csrf
                <div class="form-group mb-4 text-center">
                    <label for="otp" class="form-label d-block fw-semibold mb-2" style="font-size: 14px; color: #334155;">Enter 6-Digit Code</label>
                    <input type="text" name="otp" id="otp" maxlength="6" autofocus
                           class="form-control text-center mx-auto"
                           placeholder="······"
                           style="max-width: 280px; height: 56px; font-size: 28px; letter-spacing: 12px; font-weight: 700; color: #003f7d; border-radius: 10px; border: 2px solid #cbd5e1;"
                           required pattern="\d{6}" inputmode="numeric">
                    <small class="text-muted d-block mt-2">Code expires in 10 minutes.</small>
                </div>

                <div class="accor-btn">
                    <button type="submit" class="cta cta_white">Verify &amp; Sign In</button>
                </div>
            </form>

            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top" style="font-size: 13.5px;">
                <form action="{{ route('admin.2fa.resend') }}" method="POST" class="d-inline">
                    @csrf
                    <span class="text-muted">Didn't receive it?</span>
                    <button type="submit" class="btn btn-link p-0 ms-1 fw-semibold" style="color: #003f7d; text-decoration: underline; font-size: 13.5px; border: none; background: none;">
                        Resend code
                    </button>
                </form>

                <a href="{{ route('login') }}" class="text-muted text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Back to sign in
                </a>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const otpInput = document.getElementById('otp');
        if (otpInput) {
            otpInput.focus();
            otpInput.addEventListener('input', function (e) {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length === 6) {
                    document.getElementById('admin2faForm').submit();
                }
            });
        }
    });
</script>
@endsection
