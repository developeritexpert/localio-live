@extends('user_layout.master')
@section('content')
    <section class="banner_sec help-cntr-bnr inr-bnr dark lg_Bnr" style="background-color: #003F7D;">
        <div class="bubble-wrp">
            <img src="{{ asset('front/img/small-bnnr-bg.png') ?? '' }}" alt="">
        </div>
    </section>
    <section class="contact_sec login_form p_120 pt-0 light">
        <div class="container">
            <div class="contact_content" data-aos="fade-up" data-aos-duration="1000">
                <div class="hd_text">
                    <h2 class="text-center">New Password</h2>
                </div>
                <!-- Continue with the rest of your form -->
                <form class="password_form" action="{{ route('new-password-procc', ['locale' => app()->getLocale()]) }}"
                    method="post">
                    @csrf
              
            
                    <div class="form-group">
                        <div class="input-box mt-0">
                            <x-google-input
                                type="password"
                                name="password"
                                id="new-password"
                                label="New Password"
                            />
                        </div>
                        <span id="togglePassword" class="eye-icon" style="cursor: pointer;">
                            <i class="fa fa-eye-slash"></i>
                        </span>
                    </div>
                    
                    <div class="form-group">
                        <div class="input-box mt-0">
                            <x-google-input
                                type="password"
                                name="password_confirmation"
                                id="password-confirm"
                                label="Confirm New Password"
                            />
                        </div>
                        <span id="togglePasswordConfirm" class="eye-icon" style="cursor: pointer;">
                            <i class="fa fa-eye-slash"></i>
                        </span>
                    </div>
                    

                    {{-- <div class="form-row align-items-center">
                        <div class="col">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                                <label class="form-check-label small" for="rememberMe">Remember me</label>
                            </div>
                        </div>
                    </div> --}}
                
                        <button type="submit" class="cta cta_white">Save</button>
                  
                    <p class="new-accnt text-center mt-4">
                        New to Localio? <a href="{{ route('register') }}" class="sk_blu big-bld">Sign up</a>
                    </p>
                </form>
            </div>
        </div>
    </section>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form.password_form');
            const passwordInput = document.getElementById('new-password');
            const confirmInput = document.getElementById('password-confirm');
            const togglePassword = document.getElementById('togglePassword');
            const toggleConfirm = document.getElementById('togglePasswordConfirm');
    
            // Safety checks
            if (!form || !passwordInput || !confirmInput) return;
    
            // Toggle password visibility
            togglePassword?.addEventListener('click', function () {
                const icon = this.querySelector('i');
                const isHidden = passwordInput.type === 'password';
                passwordInput.type = isHidden ? 'text' : 'password';
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
    
            toggleConfirm?.addEventListener('click', function () {
                const icon = this.querySelector('i');
                const isHidden = confirmInput.type === 'password';
                confirmInput.type = isHidden ? 'text' : 'password';
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
    
            // Form validation
            form.addEventListener('submit', function (e) {
                removeErrors();
                let valid = true;
    
                const password = passwordInput.value.trim();
                const confirmPassword = confirmInput.value.trim();
    
                if (password.length < 8) {
                    showError(passwordInput, 'Password must be at least 8 characters.');
                    valid = false;
                }
    
                if (password !== confirmPassword) {
                    showError(confirmInput, 'Passwords do not match.');
                    valid = false;
                }
    
                if (!valid) {
                    e.preventDefault();
                }
            });
    
            // Live input cleanup
            [passwordInput, confirmInput].forEach(input => {
                input.addEventListener('input', function () {
                    removeFieldError(input);
                });
            });
    
            function showError(input, message) {
                const formGroup = input.closest('.form-group');
                const inputBox = input.closest('.input-box');
    
                if (!formGroup || !inputBox) return;
    
                inputBox.classList.add('error');
    
                if (!formGroup.querySelector('.text-danger')) {
                    const error = document.createElement('div');
                    error.className = 'text-danger small mt-1';
                    error.textContent = message;
                    formGroup.appendChild(error);
                }
            }
    
            function removeErrors() {
                form.querySelectorAll('.text-danger').forEach(el => el.remove());
                form.querySelectorAll('.input-box.error').forEach(el => el.classList.remove('error'));
            }
    
            function removeFieldError(input) {
                const formGroup = input.closest('.form-group');
                const inputBox = input.closest('.input-box');
    
                if (!formGroup || !inputBox) return;
    
                inputBox.classList.remove('error');
                const error = formGroup.querySelector('.text-danger');
                if (error) error.remove();
            }
        });
    </script>
    
        
        
        
    
@endsection

