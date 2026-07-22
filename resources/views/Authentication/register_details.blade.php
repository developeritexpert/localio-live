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
                    <h2 class="text-center">Create your Profile</h2>
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
                                <x-google-input type="text" name="first_name" label="First Name" id="firstName" value="{{ old('first_name', $firstName) }}"/>
                            </div>
                        </div>

                        <!-- Last Name Input Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-google-input type="text" name="last_name" label="Last Name" id="lastName" value="{{ old('last_name', $lastName) }}"/>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group form-group_m">
                                <x-google-input type="text" name="job_title" label="Job Title" id="jobTitle" value="{{ old('job_title') }}" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group form-group_m">
                                <label for="companySize" style="font-weight: 500; margin-bottom: 5px; display: block; color: #495057;">Company Type/Size</label>
                                <select name="company_size" id="companySize" class="form-control" style="border: 1px solid #ced4da; border-radius: 4px; height: 50px; padding: 10px 15px; color: #495057; width: 100%;">
                                    <option value="" disabled {{ old('company_size') ? '' : 'selected' }}>Select Company Type/Size</option>
                                    <option value="1" {{ old('company_size') == '1' ? 'selected' : '' }}>Freelance / Solo</option>
                                    <option value="2" {{ old('company_size') == '2' ? 'selected' : '' }}>Small Business (1-50 emp.)</option>
                                    <option value="3" {{ old('company_size') == '3' ? 'selected' : '' }}>Mid-Market (51-1000 emp.)</option>
                                    <option value="4" {{ old('company_size') == '4' ? 'selected' : '' }}>Enterprise (&gt;1000 emp.)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="accor-btn" style="margin-top: 25px;">
                        <button type="submit" class="cta cta_white register_details_btn" style="width: 100%;">Sign Up</button>
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
