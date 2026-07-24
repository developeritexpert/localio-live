@extends('user_layout.master')
@section('content')
    @php
        $isModal = request()->has('modal') || session('register_from_modal');
    @endphp

    @if($isModal)
        <div class="modal-overlay fixed inset-0 z-[9999] d-flex align-items-center justify-content-center p-3" style="background: rgba(0, 0, 0, 0.5); position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow-y: auto;">
            <div class="modal-content bg-white shadow-lg relative border-0 my-auto" style="max-width: 440px; width: 100%; padding: 40px 36px 36px 36px; border-radius: 16px !important; background: #ffffff;">
                <div class="text-center mb-4">
                    <h3 class="fw-bold mb-2" style="color: #002655; font-size: 22px;">Create your profile</h3>
                    <p class="text-muted m-0" style="font-size: 13.5px;">Please provide a few more details to set up your account.</p>
                </div>
                
                @if ($errors->any())
                    <div class="alert alert-danger p-2 mb-3 small">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="register_form" action="{{ route('register.details.store', ['locale'=> getCurrentLocale()]) }}" method="post" id="registerDetailsForm">
                    @csrf
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="text" name="first_name" id="firstName" class="form-control" placeholder="First name" value="{{ old('first_name', $firstName) }}" required>
                                <label for="firstName">First name</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-floating">
                                <input type="text" name="last_name" id="lastName" class="form-control" placeholder="Last name" value="{{ old('last_name', $lastName) }}" required>
                                <label for="lastName">Last name</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" name="job_title" id="jobTitle" class="form-control" placeholder="Job title" value="{{ old('job_title') }}" required>
                        <label for="jobTitle">Job title</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select name="company_size" id="companySize" class="form-select" required>
                            <option value="" selected disabled hidden></option>
                            <option value="1" {{ old('company_size') == '1' ? 'selected' : '' }}>Freelance / Solo</option>
                            <option value="2" {{ old('company_size') == '2' ? 'selected' : '' }}>Small Business (1-50 emp.)</option>
                            <option value="3" {{ old('company_size') == '3' ? 'selected' : '' }}>Mid-Market (51-1000 emp.)</option>
                            <option value="4" {{ old('company_size') == '4' ? 'selected' : '' }}>Enterprise (&gt;1000 emp.)</option>
                        </select>
                        <label for="companySize">Company size</label>
                    </div>

                    <div class="accor-btn mt-4">
                        <button type="submit" class="cta cta_white register_details_btn w-100 py-3 fw-bold" style="background-color: #06498b; color: white; border-radius: 30px; font-size: 15px; transition: background 0.2s;">Sign up</button>
                    </div>
                </form>
            </div>
        </div>
    @else
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
                                <x-google-input type="text" name="first_name" label="First name" id="firstName" value="{{ old('first_name', $firstName) }}"/>
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
                        <button type="submit" class="cta cta_white register_details_btn" style="width: 100%; text-transform: none;">Sign up</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    @endif

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
