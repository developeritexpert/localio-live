@section('title', 'Account Configuration | Localio')

@extends('user_dashboard_layout.master')

@section('content')
<div class="col-lg-9 p-0">
    <div class="user_content user_info">

        <div class="uer_nm">
            <h1>Vendor Configuration</h1>
        </div>
                        <!-- Personal Data -->
                        <div class="reward-main-inner">
                            <div class="rewrd-inner-hd">
                                <h4>Password</h4>
                            </div>
                            <div class="rewrd-innr-btm d-flex">
                                <form method="POST" action="{{ route('vendor-updatePassword', ['locale' => app()->getLocale()]) }}"  enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-wrp">
                                                <div class="password-wrapper">

                                                    <x-google-input
                                                        type="password"
                                                        name="old_password"
                                                        id="old_password"
                                                        class="password-input"
                                                        label="Current Password"
                                                    />
                                                    <span class="eye-icon" onclick="togglePassword(this)">
                                                        <i class="fa fa-eye-slash"></i>
                                                    </span>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-wrp">
                                                <div class="password-wrapper">

                                                    <x-google-input
                                                        type="password"
                                                        name="new_password"
                                                        id="new_password"
                                                        label="New Password"
                                                    />
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
                                                    <x-google-input
                                                        type="password"
                                                        name="new_password_confirmation"
                                                        id="new_password_confirmation"
                                                        label="Confirm New Password"
                                                    />
                                                </div>

                                            </div>
                                        </div>


                                    <br>

                                    <button class="blue-btn" type="submit">Save Changes</button>
                                </form>
                            </div>
                        </div>


                        <!-- Personal Data -->
                        {{-- <div class="reward-main-inner mt-2">
                            <div class="rewrd-inner-hd">
                                    <h4 class="mb-0">Email Preferences</h4>
                            </div>

                            <div class="rewrd-innr mt-3">
                                    <div class="row justify-content-center">
                                        <div class="">
                                            <div class="card-body p-4">
                                                <div class="row g-4">
                                                    @foreach ($preferences as $pref)
                                                        <div class="col-12">
                                                            <div class="d-flex align-items-start">
                                                                <div class="email-icon me-3">
                                                                    <i class="{{ $pref['icon'] }}"></i>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <div class="fw-semibold">
                                                                        {{ ucwords(str_replace('_', ' ', $pref['title'])) }}
                                                                    </div>

                                                                    <div class="small text-muted">{{ $pref['desc'] }}</div>
                                                                </div>
                                                                <div class="form-check form-switch ms-3">
                                                                    <input type="checkbox"
                                                                           class="form-check-input email-toggle"
                                                                           data-template-id="{{ $pref['template_id'] }}"
                                                                           {{ $pref['value'] ? 'checked' : '' }}>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <div id="emailPrefMessage" class="mt-3 text-success fw-semibold" style="display: none;">
                                                    Preferences updated!
                                                </div>
                                            </div>

                                            <div class="p-3 border-top">
                                                <div class="mt-2 text-muted small">
                                                    Changes will take effect immediately.
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                            </div>
                        </div> --}}

        </div>
    </div>
</div>


@endsection


@push('scripts')
<script>
    $(document).ready(function () {
        $('.email-toggle').on('change', function () {
            let templateId = $(this).data('template-id');
            let isEnabled = $(this).is(':checked');

            $.ajax({
                url: "{{ route('user.email-preferences.update', app()->getLocale()) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    template_id: templateId,
                    enabled: isEnabled ? 1 : 0
                },
                success: function (res) {
                    Swal.fire({
                        toast: true,
                        icon: 'success',
                        title: res.message || 'Preference updated.',
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                },
                error: function (xhr) {
                    let res = xhr.responseJSON;
                    Swal.fire({
                        toast: true,
                        icon: 'error',
                        title: res?.message || 'Something went wrong.',
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                }
            });
        });
    });
</script>
@endpush

