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
                    <h2 class="text-center">{{ static_text('register_to_localio') }}</h2>
                    <p class="text-center">{{ static_text('create_an_account') }}</p>
                    {{-- <p class="text-center">as</p>


                    <h2 class="text-center"> <a href="{{ route('vendor-register') }}">Vendor Sign Up</a></h2> --}}

                </div>
                <div class="scl_login">
                    <div class="row ">
                        <div class="col-6">
                            <div class="login_box size18" style="
                            text-align: right;
                        ">
                                <a href="{{ route('google.login') }}" class="login_link" style="background: #DB4437;">
                                    <span class="scl-icn"><i class="fa-brands fa-google"></i></span>
                                    {{-- Login with Google --}}
                                </a>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="login_box size18">
                                <a href="{{ route('login.facebook') }}" class="login_link">
                                    <span class="scl-icn"><i class="fa-brands fa-facebook"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Your existing form -->
                <div class="or-separator">
                    <span class="size16">or</span>
                </div>
                <!-- Continue with the rest of your form -->
                <form class="register_form" action="{{ route('user-register-process', ['locale'=> getCurrentLocale()]) }}" method="post" id="registerForm">
                    @csrf
                    <div class="row">
                        <!-- First Name Input Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                {{-- <input type="text" class="form-control" name="first_name" id="firstName" placeholder="First Name"> --}}
                                <x-google-input type="text" name="first_name" label="First Name" id="firstName"/>
                               
                            </div>
                        </div>

                        <!-- Last Name Input Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                {{-- <input type="text" class="form-control" name="last_name" id="lastName" placeholder="Last Name"> --}}
                                <x-google-input type="text" name="last_name" label="Last Name" id="lastName"/>
                             
                            </div>
                        </div>
                    </div>
                    <div class="row">

                            <div class="form-group form-group_m ">
                                {{-- <input type="email" class="form-control" name="email" id="emailAddress" placeholder="Email"> --}}

                                <x-google-input type="email" name="email" label="Email" id="emailAddress" :attributes="['autocomplete' => 'off']" />

           

                            </div>


                    </div>
                    <div class="row">
                        <div class="form-group form-group_m">
                            <x-google-input type="text" name="job_title" label="Job Title" id="jobTitle" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group form-group_m">
                            <select name="company_size" id="companySize" class="form-control" style="border: 1px solid #ced4da; border-radius: 4px; height: 50px; padding: 10px 15px; color: #495057;">
                                <option value="" disabled selected>Select Company Type/Size</option>
                                <option value="Freelance/Solo">Freelance / Solo</option>
                                <option value="Small Business (1-50 emp.)">Small Business (1-50 emp.)</option>
                                <option value="Mid-Market (51-1000 emp.)">Mid-Market (51-1000 emp.)</option>
                                <option value="Enterprise (>1000 emp.)">Enterprise (>1000 emp.)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group form-group_m">
                            {{-- <input type="password" class="form-control" id="password" name="password" placeholder="Password"> --}}
                            <x-google-input type="password" name="password" id="register-password"  label="Password" :attributes="['autocomplete' => 'new-password']" />
                            <span id="togglePassword" class="eye-icon" style="cursor: pointer;">
                                <i class="fa fa-eye-slash"></i>
                            </span>
                
                        </div>
                    </div>
                    <div class="accor-btn">
                        <button type="submit" class="cta cta_white register_btn">{{ static_text('sign_up') }}</button>
                    </div>
                    <p class="new-accnt text-center" style="margin-top: 10px; margin-bottom: 2px; ">
                        {{ static_text('already_to_localio') }} <a href="{{ route('login') }}" class="sk_blu big-bld"  onmouseover="this.style.color='#f9633b'"
                        onmouseout="this.style.color='#06498b'">{{ static_text('login') }}</a>
                    </p>
                </form>
            </div>
        </div>
    </section>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {
            const form = $('#registerForm');
    
            $('.register_btn').click(function (e) {
                e.preventDefault();
                removeErrors();
    
                let valid = true;
    
                form.find('input, select').each(function () {
                    const input = $(this);
                    const value = input.val().trim();
                    const inputName = input.attr('name');

                    const inputBox = input.closest('.input-box');
                    const formGroup = input.closest('.form-group');

                    let message = '';

                    // Email validation
                    if (input.attr('type') === 'email') {
                        if (value === '') {
                            message = 'Please enter an email.';
                        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                            message = 'Please enter a valid email address.';
                        }

                    // Password validation
                    } else if (input.attr('type') === 'password') {
                        if (value === '') {
                            message = 'Please enter a password.';
                        } else if (value.length < 8) {
                            message = 'Password must be at least 8 characters.';
                        }

                    // First name / Last name validation
                    } else if (['first_name', 'last_name'].includes(inputName)) {
                        if (value === '') {
                            message = 'This field is required.';
                        } else if (!/^[a-zA-Z\s'-]+$/.test(value)) {
                            message = 'Only Letters & spaces allowed.';
                        }

                    // Generic required field
                    } else if (input.attr('type') === 'text' && value === '') {
                        message = 'This field is required.';
                    }

                    // Show error if needed
                    if (message !== '') {
                        inputBox.addClass('error');
                        if (formGroup.find('.text-danger').length === 0) {
                            formGroup.append('<div class="text-danger small">' + message + '</div>');
                        }
                        valid = false;
                    }
                });

    
                if (valid) {
                    form.submit();
                }
            });
    
            // Remove errors on typing
            form.find('input').on('input', function () {
                const inputBox = $(this).closest('.input-box');
                const formGroup = $(this).closest('.form-group');
    
                inputBox.removeClass('error');
                formGroup.find('.text-danger').remove();
            });
    
            // Remove all errors
            function removeErrors() {
                form.find('.text-danger').remove();
                form.find('.input-box').removeClass('error');
            }
    
            // Toggle password visibility
            setTimeout(() => {
                $('#togglePassword').on('click', function () {
                    const $input = $('#register-password');
                    const $icon = $(this).find('i');

                    if ($input.attr('type') === 'password') {
                        $input.attr('type', 'text');
                        $icon.removeClass('fa-eye-slash').addClass('fa-eye');
                    } else {
                        $input.attr('type', 'password');
                        $icon.removeClass('fa-eye').addClass('fa-eye-slash');
                    }
                });
            }, 100); // slight delay for Livewire DOM update

    

        });
    </script>
    
        
@endsection
