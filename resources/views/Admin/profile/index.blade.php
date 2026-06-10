@extends('admin_layout.master')
@section('content')
    <div class="card card-bordered h-100">
        <div class="card-inner">
            <div class="card-head">
                <h5 class="card-title">Admin profile
                </h5>
            </div>
            <div class="row ">
                <div class="col-lg-12">
                    <form action="{{ url('admin-dashboard/update-profile-procc') }}" method="POST" id="profileForm">
                        @csrf
                        <input type="hidden" name="id" value="{{ Auth::user()->id }}">
                        <div class="row">
                            <div class="col-lg-6 p-3">
                                <div class="form-group">
                                    <label class="form-label" for="first_name">First Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" name="first_name" class="form-control" id="first_name"
                                            value="{{ Auth::user()->first_name }}" >
                                    </div>
                                    @error('first_name')
                                        <span class="text text-danger">{{ $message }}</span>
                                    @enderror
                                    <span class="text text-danger" id="first_name_error"></span>
                                </div>
                            </div>
                            <div class="col-lg-6 p-3">
                                <div class="form-group">
                                    <label class="form-label" for="last_name">Last Name</label>
                                    <div class="form-control-wrap">
                                        <input type="text" name="last_name" class="form-control" id="last_name"
                                            value="{{ Auth::user()->last_name }}" >
                                    </div>
                                    @error('last_name')
                                        <span class="text text-danger">{{ $message }}</span>
                                    @enderror
                                    <span class="text text-danger" id="last_name_error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 p-3">
                                <div class="form-group">
                                    <label class="form-label" for="name"> Email</label>
                                    <div class="form-control-wrap">
                                        <input type="email" name="email" class="form-control" id="email"
                                            value="{{ Auth::user()->email }}" >
                                    </div>
                                    @error('email')
                                        <span class="text text-danger">{{ $message }}</span>
                                    @enderror
                                    <span class="text text-danger" id="email_error"></span>
                                </div>
                            </div>
                            <div class="col-lg-6 p-3">
                                <div class="form-group">
                                    <label class="form-label" for="name">Phone Number</label>
                                    <div class="form-control-wrap">
                                        <input type="number" name="number" class="form-control" id="number"
                                            value="{{ Auth::user()->number ?? '' }}">
                                    </div>
                                    @error('number')
                                        <span class="text text-danger">{{ $message }}</span>
                                    @enderror
                                    <span class="text text-danger" id="number_error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-lg btn-primary btn-localio">Update </button>
                        </div>
                    </form>
                </div>
                <div class="card card-bordered card-preview d-none p-3 my-5" id="changepasswordCard">
                    <div class="card-inner">
                        <div class="preview-block">
                            <div class="d-flex justify-content-between">
                                <span class="preview-title-lg overline-title">Change Password</span>
                                <span class="close"><i class="fas fa-times"></i></span>
                            </div>
                            <div class="row gy-4">
                                <div class="col-sm-6">
                                    <form action="{{ url('admin-dashboard/change-password-procc') }}" method="POST" id="passwordForm">
                                        @csrf
                                        <div class="form-group">
                                            <label class="form-label" for="name"> Old Password</label>
                                            <div class="form-control-wrap">
                                                <input type="password" class="form-control" name="old_password"
                                                    id="old_password" placeholder="Old Password" >
                                                @error('old_password')
                                                    <span class="text text-danger">{{ $message }}</span>
                                                @enderror
                                                <span class="text text-danger" id="old_password_error"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="name"> New Password</label>
                                            <div class="form-control-wrap">
                                                <input type="password" class="form-control" name="new_password"
                                                    id="new_password" placeholder="New Password" >
                                                @error('new_password')
                                                    <span class="text text-danger">{{ $message }}</span>
                                                @enderror
                                                <span class="text text-danger" id="new_password_error"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="name"> Confirm Password</label>
                                            <div class="form-control-wrap">
                                                <input type="password" class="form-control" name="new_password_confirmation"
                                                    id="new_password_confirmation" placeholder="Confirm Password" >
                                                @error('new_password_confirmation')
                                                    <span class="text text-danger">{{ $message }}</span>
                                                @enderror
                                                <span class="text text-danger" id="new_password_confirmation_error"></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary add btn-localio"
                                                id="add"><span id="button_value">Update</span></button>

                                        </div>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group col-lg-12 mt-3 text-right">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-primary add-new btn-localio" id="changepass">
                        <span>Change Password</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#changepass').click(function() {
            $('#changepasswordCard').removeClass('d-none');
            $(this).hide();

        });
        $('.close').click(function() {
            $('#changepasswordCard').addClass('d-none');
            $('#changepass').show();
        });

        // Frontend validation for profile form
        $('#profileForm').on('submit', function(e) {
            let isValid = true;

            // Clear previous errors
            $('#first_name_error, #last_name_error, #email_error, #number_error').text('');

            // First name validation
            const firstName = $('#first_name').val().trim();
            if (firstName === '') {
                $('#first_name_error').text('First name is required');
                isValid = false;
            } else if (firstName.length < 2) {
                $('#first_name_error').text('First name must be at least 2 characters');
                isValid = false;
            }

            // Last name validation
            const lastName = $('#last_name').val().trim();
            if (lastName === '') {
                $('#last_name_error').text('Last name is required');
                isValid = false;
            } else if (lastName.length < 2) {
                $('#last_name_error').text('Last name must be at least 2 characters');
                isValid = false;
            }

            // Email validation
            const email = $('#email').val().trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email === '') {
                $('#email_error').text('Email is required');
                isValid = false;
            } else if (!emailRegex.test(email)) {
                $('#email_error').text('Please enter a valid email address');
                isValid = false;
            }

            // Phone number validation (optional but if provided, must be valid)
            const phoneNumber = $('#number').val().trim();
            if (phoneNumber !== '') {
                if (phoneNumber.length < 10 || phoneNumber.length > 15) {
                    $('#number_error').text('Phone number must be between 10-15 digits');
                    isValid = false;
                } else if (!/^\d+$/.test(phoneNumber)) {
                    $('#number_error').text('Phone number must contain only digits');
                    isValid = false;
                }
            }

            if (!isValid) {
                e.preventDefault();
            }
        });

        // Frontend validation for password form
        $('#passwordForm').on('submit', function(e) {
            let isValid = true;

            // Clear previous errors
            $('#old_password_error, #new_password_error, #new_password_confirmation_error').text('');

            // Old password validation
            const oldPassword = $('#old_password').val().trim();
            if (oldPassword === '') {
                $('#old_password_error').text('Old password is required');
                isValid = false;
            }

            // New password validation
            const newPassword = $('#new_password').val().trim();
            if (newPassword === '') {
                $('#new_password_error').text('New password is required');
                isValid = false;
            } else if (newPassword.length < 8) {
                $('#new_password_error').text('New password must be at least 8 characters');
                isValid = false;
            } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(newPassword)) {
                $('#new_password_error').text('Password must contain at least one uppercase letter, one lowercase letter, and one number');
                isValid = false;
            }

            // Confirm password validation
            const confirmPassword = $('#new_password_confirmation').val().trim();
            if (confirmPassword === '') {
                $('#new_password_confirmation_error').text('Confirm password is required');
                isValid = false;
            } else if (newPassword !== confirmPassword) {
                $('#new_password_confirmation_error').text('Passwords do not match');
                isValid = false;
            }

            // Check if old and new password are the same
            if (oldPassword === newPassword && oldPassword !== '') {
                $('#new_password_error').text('New password must be different from old password');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });

        // Real-time validation for password confirmation
        $('#new_password_confirmation').on('input', function() {
            const newPassword = $('#new_password').val();
            const confirmPassword = $(this).val();

            if (confirmPassword !== '' && newPassword !== confirmPassword) {
                $('#new_password_confirmation_error').text('Passwords do not match');
            } else {
                $('#new_password_confirmation_error').text('');
            }
        });
    </script>
@endsection
