@extends('user_layout.master')
@section('meta_title', isset($contact) && isset($contact->meta_title) ? $contact->meta_title : 'Contact Page')
@section('meta_description', isset($contact) && isset($contact->meta_description) ? $contact->meta_description : '')
@section('content')

<section class="banner_sec help-cntr-bnr inr-bnr dark" style="background-color: #003F7D;">
    <div class="bubble-wrp">
        <img src="{{ asset('front/img/small-bnnr-bg.png') }}" alt="">
    </div>
    <div class="banner_content">
        <div class="container">
            <div class="banner_row" data-aos="fade-up" data-aos-duration="1000">
                <div class="banner_text_col">
                    <div class="banner_content_inner">
                    <h1>{{ $contact->contact_heading ?? '' }}</h1>
                    <p>{{ $contact->contact_description ?? '' }}</p>
                    </div>
                </div>
                <div class="banner_image_col">
                    <div class="banner_image">
                    <img src="{{asset($contact->image_first) }}" class="banner_top_image">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: "{{ session('success') }}"
    });
});
</script>
@endif

<section class="contact_sec login_form light">
    <div class="container">
        <div class="contact_content aos-init aos-animate my-2 mx-auto" data-aos="fade-up" data-aos-duration="1000">
            <div class="hd_text">
                <h2 class="text-center">{{ static_text('contact_us') }}</h2>
            </div>

             <!-- Sub Heading -->
             {{-- <div class="col-md-12">
                <label class="form-label" for="sub_heading">Heading</label>
                <input type="text" class="form-control" id="sub_heading" name="sub_heading"
                    value="{{ $whoWeAre->sub_heading ?? '' }}" />
                    <br>
            </div> --}}



            <form action="{{ route('query.submit', ['locale' => session('lang_code', 'en-us')]) }}" method="POST" class="conatct_from" enctype="multipart/form-data">
                @csrf

                {{-- Query Dropdown (used as reason_id) --}}
                @php
                    $queryOptions = [
                        1 => 'General Inquiry',
                        2 => 'Support',
                        3 => 'Feedback',
                        4 => 'Business Collaboration'
                    ];
                @endphp

                <div class="form-group">
                    <x-google-input
                        type="select"
                        name="query_type"
                        id="query_type"
                        label="Choose a Query"
                        :options="$queryOptions"
                        :value="old('query_type')"
                        placeholder="Select a topic"
                        :alwaysActive="true"
                    />
                </div>

                <div class="form-group">
                    <x-google-input
                        type="text"
                        name="name"
                        id="name"
                        label="Full Name"
                        :value="old('name')"
                        placeholder="Enter your full name"
                        :alwaysActive="true"
                    />
                </div>

                <div class="form-group">
                    <x-google-input
                        type="email"
                        name="email"
                        id="email"
                        label="Email Address"
                        :value="old('email')"
                        placeholder="Enter your email"
                        :alwaysActive="true"
                    />
                </div>


                {{-- Message --}}
                <div class="textarea-upload-wrapper position-relative">
                    <x-google-input
                        type="textarea"
                        name="message"
                        id="message"
                        label="Your Message"
                        :value="old('message')"
                        rows="8"
                        :alwaysActive="true"
                    />

                    {{-- <label for="upload_input" class="upload-icon">
                        <img src="https://legalio.mx/assets/img/Group1.svg" alt="Upload" />
                    </label>
                    <input type="file" id="upload_input" name="attachment" class="d-none" /> --}}
                </div>

                {{-- Submit --}}
                <div class="top-pro-btn snd_bttn">
                    <button
                        type="button"
                        class="btn cta cta_orange"
                        id="contact-submit-btn"
                    
                    >
                        {{ static_text('send_message') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<section class="right_tool_sec dark p_80">
    <div class="container">
        <div class="right-tool-wrp text-center" data-aos="fade-up" data-aos-duration="1000">
            <div class="otr_rgtool">
                <h2>{{ $homeContents['find_tool'] ?? null }}</h2>
            </div>
            <div class="right-tool-pack">
                <div class="row">
                    @foreach ($pageTileTranslationRightTool as $tile)
                        @php
                            $translation = $tile->translations->first();
                        @endphp
                        <div class="col-lg-4">
                            <div class="tool-card">
                                <div class="tool-card-img">
                                    @if (!empty($translation?->image))
                                        <img src="{{ asset($translation->image) }}" alt="">
                                    @elseif (!empty($tile->image))
                                        <img src="{{ asset($tile->image) }}" alt="">
                                    @endif
                                </div>
                                <div class="tool-crd-bdy">
                                    <h3 class="tool_hed">{{ $translation->title ?? '' }}</h3>
                                    <p class="size18">{{ $translation->description ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="right-tool-btn text-center">
                <a href="{{ route('category', ['locale' => app()->getLocale()]) }}" class="cta">
                    {{ $homeContents['get_button_lable'] ?? null }}
                </a>
            </div>
        </div>
    </div>

    <div class="back-image1">
        @if (isset($findToolLeftImage))
            <img src="{{ asset($findToolLeftImage->meta_value) }}" class="image-pattern1" alt="{{ $findToolLeftImage->meta_key }}">
        @endif
    </div>
    <div class="back-image2">
        @if (isset($findToolRightImage))
            <img src="{{ asset($findToolRightImage->meta_value) }}" class="image-pattern2" alt="{{ $findToolRightImage->meta_key }}">
        @endif
    </div>
</section>

{{-- SCRIPT 1: Handle form submission for non-authenticated users --}}
<script>
document.getElementById('contact-submit-btn')?.addEventListener('click', function() {
    const isAuth = this.getAttribute('data-auth') === '1';
    const form = document.querySelector('.conatct_from');

    form.submit();
});
</script>

{{-- SCRIPT 2: File preview and form validation (SINGLE INSTANCE) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.conatct_from');
    const queryType = document.getElementById('query_type');
    const subject = document.getElementById('subject');
    const message = document.getElementById('message');
    const fileInput = document.getElementById('upload_input');

    // Create preview container ONLY ONCE
    let previewContainer = document.getElementById('preview-container');
    if (!previewContainer) {
        const uploadWrapper = fileInput.closest('.textarea-upload-wrapper');
        previewContainer = document.createElement('div');
        previewContainer.id = 'preview-container';
        previewContainer.style.marginTop = '10px';
        uploadWrapper.appendChild(previewContainer);
        console.log('Preview container created'); // Debug
    }

    // Handle file preview
    fileInput?.addEventListener('change', function () {
        const file = this.files[0];
        previewContainer.innerHTML = ''; // Clear old preview
        console.log('File selected:', file); // Debug

        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '20%';
                img.style.borderRadius = '8px';
                previewContainer.appendChild(img);
                console.log('Preview image added'); // Debug
            };
            reader.readAsDataURL(file);
        }
    });

    // Form validation
    form.addEventListener('submit', function (e) {
        removeErrors();
        let valid = true;

        if (!queryType.value.trim()) {
            showError(queryType, 'Please select a query type.');
            valid = false;
        }

        if (!subject.value.trim()) {
            showError(subject, 'Subject is required.');
            valid = false;
        }

        if (!message.value.trim()) {
            showError(message, 'Message is required.');
            valid = false;
        }

        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                showError(fileInput, 'Only JPG, PNG or GIF images are allowed.');
                valid = false;
            } else if (file.size > 2 * 1024 * 1024) {
                showError(fileInput, 'Image must be less than 2MB.');
                valid = false;
            }
        }

        if (!valid) e.preventDefault();
    });

    function showError(input, message) {
        const parent = input.closest('.form-group') || input.closest('.textarea-upload-wrapper');
        if (!parent) return;
        input.classList.add('is-invalid');
        const error = document.createElement('div');
        error.className = 'text-danger small mt-1';
        error.innerText = message;
        parent.appendChild(error);
    }

    function removeErrors() {
        form.querySelectorAll('.text-danger').forEach(el => el.remove());
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    }
});
</script>

{{-- SCRIPT 3: Restore form data after login --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const storedData = localStorage.getItem('pendingContactForm');

    if (storedData && {{ auth()->check() ? 'true' : 'false' }}) {
        console.log('Authenticated user found with stored data'); // Debug
        const form = document.querySelector('.conatct_from');
        const data = JSON.parse(storedData);
        console.log('Restoring data:', data); // Debug

        // Restore text field values
        Object.keys(data).forEach(key => {
            if (key.startsWith('_file_')) return; // Skip file keys

            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                input.value = data[key];
                // Trigger change event for UI components
                input.dispatchEvent(new Event('change', { bubbles: true }));
                console.log(`Restored ${key}:`, data[key]); // Debug
            }
        });

        // Handle file restoration
        if (data._file_base64 && data._file_name && data._file_type) {
            console.log('Restoring file:', data._file_name); // Debug

            // Convert base64 to blob then to File
            fetch(data._file_base64)
                .then(res => res.blob())
                .then(blob => {
                    // Create proper File object
                    const restoredFile = new File([blob], data._file_name, {
                        type: data._file_type,
                        lastModified: new Date().getTime()
                    });

                    console.log('File object created:', restoredFile); // Debug

                    // Use DataTransfer to set the file
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(restoredFile);

                    const fileInput = document.getElementById('upload_input');
                    fileInput.files = dataTransfer.files;

                    console.log('File set to input:', fileInput.files[0]); // Debug

                    // Ensure preview container exists
                    let previewContainer = document.getElementById('preview-container');
                    if (!previewContainer) {
                        const uploadWrapper = fileInput.closest('.textarea-upload-wrapper');
                        previewContainer = document.createElement('div');
                        previewContainer.id = 'preview-container';
                        previewContainer.style.marginTop = '10px';
                        uploadWrapper.appendChild(previewContainer);
                    }

                    // Show preview
                    previewContainer.innerHTML = '';
                    const img = document.createElement('img');
                    img.src = data._file_base64;
                    img.style.maxWidth = '20%';
                    img.style.borderRadius = '8px';
                    previewContainer.appendChild(img);

                    console.log('Preview restored, submitting form...'); // Debug

                    // Submit form after restoration
                    setTimeout(() => {
                        console.log('Submitting form with file:', fileInput.files[0]); // Debug
                        form.submit();
                        localStorage.removeItem('pendingContactForm');
                    }, 1000);
                })
                .catch(error => {
                    console.error('File restoration error:', error);
                    // Submit without file if error
                    setTimeout(() => {
                        form.submit();
                        localStorage.removeItem('pendingContactForm');
                    }, 500);
                });
        } else {
            console.log('No file to restore, submitting form...'); // Debug
            // No file, submit with text data only
            setTimeout(() => {
                form.submit();
                localStorage.removeItem('pendingContactForm');
            }, 500);
        }
    }
});
</script>

@if(Session::has('ticket_success'))
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const successData = @json(Session::get('ticket_success'));
        const locale = "{{ session('lang_code', 'en-us') }}";
        const supportViewUrl = `/${locale}/user-dashboard/support/${successData.ticket_id}`;
        const supportPageUrl = `/${locale}/user-dashboard/support`;

        Swal.fire({
            title: "Ticket Submitted Successfully!",
            html: `
                <p>${successData.message}</p>
                <p style="margin-top: 10px;">
                    <a href="${supportViewUrl}" style="color: #007bff; text-decoration: underline;">
                        Click here to track your ticket status
                    </a>
                </p>
            `,
            showConfirmButton: true,
            confirmButtonText: "Go to Support Page",
            showClass: {
                popup: 'swal2-show'
            },
            hideClass: {
                popup: ''
            },
            customClass: {
                popup: 'custom-swal-popup'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = supportPageUrl;
            }
        });
    });
</script>

<script>
    $(window).on('load', function() {
        $('body').addClass('SuportContPgCls');
    });
</script>
@endif

@endsection
