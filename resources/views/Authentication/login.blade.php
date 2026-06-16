@extends('user_layout.master')
@section('content')
    <section class="banner_sec help-cntr-bnr inr-bnr dark lg_Bnr" style="background-color: #003F7D;">
        <div class="bubble-wrp">
            <img src="{{ asset('front/img/small-bnnr-bg.png') ?? '' }}" alt="">
        </div>
    </section>
    <section class="contact_sec login_form  p_120 pt-0 light">
        <div class="container">
            <div class="contact_content" data-aos="fade-up" data-aos-duration="1000">
                <div class="hd_text">
                    <h2 class="text-center">{{static_text('login_to_your_account')}}</h2>
                    <p class="text-center">{{ static_text('welcome_back') }}</p>
                </div>
                <div class="scl_login">
                    <div class="row">
                        <div class="col-6">
                            <div class="login_box size18">
                                <div class="l_goin1">
                                <a href="{{ route('google.login') }}" class="login_link" onclick="openGoogleLogin(event)" style="background: #DB4437;">
                                    <span class="scl-icn"><i class="fa-brands fa-google"></i></span>
                                    {{-- Login with Google --}}
                                </a>
                            </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="login_box size18">
                                <div class="l_goin2">
                                <a href="{{ route('login.facebook') }}" class="login_link">
                                    <span class="scl-icn"><i class="fa-brands fa-facebook"></i></span>
                                    {{-- Login with Facebook --}}
                                </a>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Your existing form -->
                <div class="or-separator">
                    <span class="size16">or</span>
                </div>
                @if (session('loginerror'))
                    <div class="text-danger">{{ session('loginerror') }}</div>
                @endif
                <!-- Continue with the rest of your form -->
                <form class="login_form" action="{{ route('login_process') }}" method="POST" autocomplete="off">
                    @csrf
                    <div class="form-group">
                    <x-google-input type="text" name="email" id="emailAddress" label="Email"  :attributes="['autocomplete' => 'off']" />
                    </div>
                    <div class="form-group">
                        {{-- <input type="password" class="form-control" id="password" name="password" placeholder="Password"> --}}
                        <x-google-input type="password" name="password" id="password"  label="Password" :attributes="['autocomplete' => 'off']" />

                        <span id="togglePassword" class="eye-icon">
                            <i class="fa fa-eye-slash"></i>
                        </span>
                    </div>
                    <div class="form-row align-items-center">
                        <div class="col frgt_btn text-right">
                            <a href="{{ route('recover-password') }}" class="small"  onmouseover="this.style.color='#f9633b'"
                            onmouseout="this.style.color='#06498b'" style="margin-top: -6px;">{{ static_text('forgot_password') }}</a>
                        </div>
                    </div>
                    <div class="accor-btn">
                        <button type="submit" class="cta cta_white">{{ static_text('login') }}</button>
                    </div>
                    <p class="new-accnt text-center" style="margin-top: 10px;margin-bottom: 2px;">
                        {{ static_text('new_to_localio') }} <a href="{{ route('register') }}" class="sk_blu big-bld"
                        onmouseover="this.style.color='#f9633b'"
                        onmouseout="this.style.color='#06498b'"
                        >{{ static_text('sign_up') }}</a>
                    </p>
                </form>
            </div>
        </div>
    </section>


<script>
    document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.login_form');
    if (!form) return;

    const emailInput = document.getElementById('emailAddress');
    const passwordInput = document.getElementById('password');

    document.getElementById('togglePassword')?.addEventListener('click', function () {

        const wrapper = icon.closest('#password');
        const input = wrapper.querySelector('input');

        if (input.type === "password") {
            input.type = "text";
            icon.querySelector('i').classList.remove('fa-eye-slash');
            icon.querySelector('i').classList.add('fa-eye');
        } else {
            input.type = "password";
            icon.querySelector('i').classList.remove('fa-eye');
            icon.querySelector('i').classList.add('fa-eye-slash');
        }

    });

    form.addEventListener('submit', function (e) {
        removeErrors();
        let valid = true;

        const emailValue = emailInput.value.trim();
        const passwordValue = passwordInput.value.trim();

        if (!emailValue) {
            showError(emailInput, "Email is required.");
            valid = false;
        } else if (!validateEmail(emailValue)) {
            showError(emailInput, "Enter a valid email address.");
            valid = false;
        }

        if (!passwordValue) {
            showError(passwordInput, "Password is required.");
            valid = false;
        } else if (passwordValue.length < 6) {
            showError(passwordInput, "Password must be at least 6 characters.");
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
        }
    });

    function showError(input, message) {
        const parentGroup = input.closest('.form-group');
        const inputBox = input.closest('.input-box');

        if (!parentGroup || !inputBox) return;

        inputBox.classList.add('error');

        if (!parentGroup.querySelector('.text-danger')) {
            const error = document.createElement('div');
            error.className = 'text-danger small';
            error.innerText = message;
            parentGroup.appendChild(error);
        }
    }

    function removeErrors() {
        const errors = form.querySelectorAll('.text-danger');
        errors.forEach(error => error.remove());

        const inputBoxes = form.querySelectorAll('.input-box.error');
        inputBoxes.forEach(box => box.classList.remove('error'));
    }

    function validateEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    // ✅ Remove error message when user starts typing
    function removeErrorOnTyping(input) {
        input.addEventListener('input', () => {
            const parentGroup = input.closest('.form-group');
            const inputBox = input.closest('.input-box');

            if (inputBox?.classList.contains('error')) {
                inputBox.classList.remove('error');
                const errorMessage = parentGroup.querySelector('.text-danger');
                if (errorMessage) errorMessage.remove();
            }
        });
    }

    // Attach listeners
    removeErrorOnTyping(emailInput);
    removeErrorOnTyping(passwordInput);
});

</script>
{{-- popup Google login script --}}
<script>
    function openGoogleLogin(event) {
        event.preventDefault();

        let width = 600;
        let height = 600;
        let left = (window.innerWidth / 2) - (width / 2);
        let top = (window.innerHeight / 2) - (height / 2);

        window.open(
            event.currentTarget.href,
            'GoogleLoginPopup',
            `width=${width},height=${height},top=${top},left=${left}`
        );
    }
    </script>

{{-- Redirect the Main window  --}}
<script>
    window.addEventListener('message', function(event) {
        if (event.origin !== window.location.origin) return;

        if (event.data.status === 'success' && event.data.redirectUrl) {
            window.location.href = event.data.redirectUrl;
        }
    });
    </script>


    @endsection

