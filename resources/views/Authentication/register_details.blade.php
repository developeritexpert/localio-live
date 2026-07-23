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
                    <h2 class="text-center">Create your profile</h2>
                    <p class="text-center">Please provide a few more details to set up your account.</p>
                </div>
                
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="register_form" action="{{ route('register.details.store', ['locale'=> getCurrentLocale()]) }}" method="post" id="registerDetailsForm">
                    @csrf
                    <div class="row">
                        <!-- First Name Input Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-google-input type="text" name="first_name" label="first name" id="firstName" value="{{ old('first_name', $firstName) }}"/>
                            </div>
                        </div>

                        <!-- Last Name Input Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-google-input type="text" name="last_name" label="Last name" id="lastName" value="{{ old('last_name', $lastName) }}"/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group form-group_m">
                                <x-google-input type="text" name="job_title" label="Job title" id="jobTitle" value="{{ old('job_title') }}" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group form-group_m">
                                <x-google-input type="select" name="company_size" id="companySize" label="Company size" :options="[
                                    '1' => 'Freelance / Solo',
                                    '2' => 'Small Business (1-50 emp.)',
                                    '3' => 'Mid-Market (51-1000 emp.)',
                                    '4' => 'Enterprise (>1000 emp.)'
                                ]" :value="old('company_size')" placeholder="Select Company size" />
                            </div>
                        </div>
                    </div>

                    <div class="accor-btn" style="margin-top: 25px;">
                        <button type="submit" class="cta cta_white register_details_btn" style="width: 100%;">Sign up</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            const form = $('#registerDetailsForm');
            
            form.on('submit', function (e) {
                removeErrors();
                let valid = true;

                form.find('input, select').each(function () {
                    const input = $(this);
                    const value = input.val() ? input.val().trim() : '';
                    const label = input.attr('name').replace('_', ' ');
                    
                    if (value === '') {
                        showError(input, 'Please enter your ' + label + '.');
                        valid = false;
                    }
                });

                if (!valid) {
                    e.preventDefault();
                }
            });

            function showError(input, message) {
                const parentGroup = input.closest('.form-group');
                const inputBox = input.closest('.input-box');

                if (inputBox.length) {
                    inputBox.addClass('error');
                }

                if (parentGroup.length && !parentGroup.find('.text-danger').length) {
                    const error = $('<div class="text-danger small" style="margin-top: 5px;">' + message + '</div>');
                    parentGroup.append(error);
                }
            }

            function removeErrors() {
                form.find('.text-danger').remove();
                form.find('.input-box.error').removeClass('error');
            }
        });
    </script>
@endsection
