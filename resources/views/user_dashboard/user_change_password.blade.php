@section('title', 'Account Configuration | Localio')

@extends('user_dashboard_layout.master')

@section('content')
<div class="col-lg-9 p-0">
    <div class="user_content user_info">
        <div class="uer_nm">
            <h1>Change Password</h1>
        </div>
        <!-- Personal Data -->
        <div class="reward-main-inner">
            <div class="rewrd-inner-hd">
                <h4>Password</h4>
            </div>
            <div class="rewrd-innr-btm ">
                <form method="POST" action="{{ route('user-updatePassword', ['locale' => app()->getLocale()]) }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-wrp">
                                <div class="password-wrapper">

                                    <x-google-input type="password" name="old_password" id="old_password"
                                        class="password-input" label="Current Password" />
                                    <span class="eye-icon" onclick="togglePassword(this)">
                                        <i class="fa fa-eye-slash"></i>
                                    </span>
                                </div>

                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-wrp">
                                <div class="password-wrapper">

                                    <x-google-input type="password" name="new_password" id="new_password"
                                        label="New Password" />
                                    <span class="eye-icon" onclick="togglePassword(this)">
                                        <i class="fa fa-eye-slash"></i>
                                    </span>
                                </div>

                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-wrp">
                                <div class="password-wrapper">
                                    <span class="eye-icon" onclick="togglePassword(this)">
                                        <i class="fa fa-eye-slash"></i>
                                    </span>
                                    <x-google-input type="password" name="new_password_confirmation"
                                        id="new_password_confirmation" label="Confirm New Password" />
                                </div>

                            </div>
                        </div>


                        <br>

                        <button class="blue-btn" type="submit">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection
