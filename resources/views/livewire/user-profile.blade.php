<div class="col-lg-9 p-0">
    <div class="user_content user_info user-profile">

        <div class="uer_nm">
            <h1>My Profile</h1>
        </div>

        <div class="profile-main">
            <div class="profile-image-component" wire:ignore.self>
                <div class="profile-main-hd d-flex align-items-center gap-3">
                    <div class="profile-img-wrapper position-relative">
                        @if ($profile_image)
                            <img src="{{ asset($profile_image) }}" alt="Profile Image" class="profile-picture">
                        @else
                            <img src="{{ dimage()}}" class="profile-picture" alt="Default User Image">
                        @endif
                        <div class="upload-overlay">
                            <input type="file" id="profileFileInput" style="display: none;" wire:model="new_profile_image" accept="image/*">
                            <label for="profileFileInput" class="upload-btn" title="Change profile picture">
                                <!-- Camera icon for profile image upload -->
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 20H4C2.89543 20 2 19.1046 2 18V8C2 6.89543 2.89543 6 4 6H7L9 4H15L17 6H20C21.1046 6 22 6.89543 22 8V18C22 19.1046 21.1046 20 20 20Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="12" cy="13" r="4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </label>
                        </div>
                    </div>
                    <button type="button" class="blue-btn" onclick="document.getElementById('profileFileInput').click()" wire:loading.attr="disabled" wire:target="new_profile_image">
                        <span wire:loading wire:target="new_profile_image">
                            <span class="spinner"></span> Processing...
                        </span>
                        <span wire:loading.remove wire:target="new_profile_image">Change Profile</span>
                    </button>
                </div>


                @error('new_profile_image')
                    <div class="error-message">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                            <path d="m15 9-6 6" stroke="currentColor" stroke-width="2"/>
                            <path d="m9 9 6 6" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        {{ $message }}
                    </div>
                @enderror

                {{-- Image Crop Modal --}}
                @if ($showCropModal && $temporaryImageUrl)
                    {{-- Include Cropper.js CSS and JS directly when modal is shown --}}
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

                    <div class="crop-modal-overlay" wire:ignore.self>
                        <div class="crop-modal">
                            <div class="crop-modal-header">
                                <h3>Crop Your Profile Image</h3>
                                <button type="button" class="modal-close-btn" wire:click="closeCropModal">×</button>
                            </div>

                            <div class="crop-container" wire:ignore>
                                <img id="cropperImage" src="{{ $temporaryImageUrl }}" alt="Image to crop">
                            </div>

                            <div class="crop-modal-footer">
                                <div class="crop-instructions">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                                        <path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    Drag to move, use corners to resize your image
                                </div>
                                <div class="crop-actions">
                                    <button type="button" class="btn-cancel" wire:click="closeCropModal">Cancel</button>
                                    <button type="button" class="btn-save" id="saveCropBtn">Save Image</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Personal Data -->
            <div class="reward-main-inner">
                <div class="rewrd-inner-hd">
                    <h4>Personal Information</h4>
                </div>
                <div class="rewrd-innr-btm d-flex">
                    <form wire:submit.prevent="updateProfile">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-wrp">
                                    {{-- <label>First Name</label> <br> --}}
                                    {{-- <input type="text" wire:model="first_name" placeholder="John"> --}}

                                    <x-google-input
                                        type="text"
                                        name="first_name"
                                        id="first_name"
                                        label="First Name"
                                        wireModel="first_name"

                                    />


                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-wrp">
                                    {{-- <label>Last Name</label> <br>
                                    <input type="text" wire:model="last_name" placeholder="Smith"> --}}
                                    <x-google-input
                                    type="text"
                                    name="last_name"
                                    id="last_name"
                                    label="Last Name"
                                    wireModel="last_name"

                                />

                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-wrp">
                                    {{-- <label>Phone Number</label> <br>
                                    <input type="number" wire:model="number" placeholder="9876543210">
                                     --}}
                                    <x-google-input
                                    type="number"
                                    name="number"
                                    id="number"
                                    label="Phone Number"
                                    wireModel="number"

                                />

                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-wrp">
                                    {{-- <label>Email</label> <br>
                                    <input type="email" wire:model="email" placeholder="John123@gmail.com"> --}}

                                    <x-google-input
                                        type="email"
                                        name="email"
                                        id="Email"
                                        label="Email"
                                        wireModel="email"

                                    />

                                </div>
                            </div>
                        </div>
                        <br>

                        {{-- <button class="blue-btn" type="submit">Save Changes</button> --}}
                    </form>
                </div>
            </div>

                        <!-- Personal Data -->
                        <div class="reward-main-inner">
                            <div class="rewrd-inner-hd">
                                <h4>Employment Information</h4>
                            </div>
                            <div class="rewrd-innr-btm d-flex">
                                <form wire:submit.prevent="updateProfile">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-wrp">
                                                {{-- <label>Job Title/Role</label> <br>
                                                <input type="text" wire:model="job_title"> --}}

                                                <x-google-input
                                                    type="text"
                                                    name="job_title"
                                                    id="Email"
                                                    label="Job Title/Role"
                                                    wireModel="job_title"
                                                />



                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-wrp">
                                                {{-- <label>Industry</label> <br>
                                                <input type="text" wire:model="industry" > --}}
                                                <x-google-input
                                                    type="text"
                                                    name="industry"
                                                    id="industry"
                                                    label="Industry"
                                                    wireModel="industry"

                                                />


                                            </div>
                                        </div>

                                        @php
                                        $company_size = [
                                           1  => '1-10',
                                           2  => '11-50',
                                            3 => '51-200',
                                            4 => '200+',
                                        ];
                                    @endphp

                                    <div class="col-lg-6">
                                        <div class="form-wrp ">
                                            <x-google-input
                                                type="select"
                                                name="company_size"

                                                id="company_size"
                                                label="Company Size"
                                                :options="$company_size"
                                                wireModel="company_size"
                                                :alwaysActive="true"
                                            />
                                        </div>
                                    </div>





                                        @php
                                        use App\Models\Country;

                                            $countries = Country::orderBy('name')->pluck('name', 'id')->toArray(); // id => name format
                                            // dd($countries);
                                            $defaultCountry = auth()->user()?->country_id;
                                        @endphp

                                        <div class="col-lg-6">
                                            <div class="form-wrp">

                                                <x-google-input
                                                    type="select"
                                                    name="country_id"
                                                    id="country_id"
                                                    label="Country / Region"
                                                    :options="$countries"
                                                    :wireModel="'country_id'"
                                                    :alwaysActive="true"
                                                />




                                            </div>
                                        </div>
                                    </div>
                                    <br>

                                    <button class="blue-btn" type="submit">Save Changes</button>
                                </form>
                            </div>
                        </div>
        </div>
        <div class="profile-main deactivate-accnt">
            <h6>Deactivate</h6>
            <p>
                Deactivating your account will disable your profile and remove your name from any content you've submitted.
            </p>

            <a href="javascript:void(0);"
               x-data
               @click="deactivateWithPassword">
               Yes, deactivate my account.
            </a>
        </div>

    </div>
</div>




    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let cropper = null;
            let cropperInitialized = false;

            // Clean up function to properly destroy cropper
            function cleanupCropper() {
                if (cropper) {
                    try {
                        cropper.destroy();
                        cropper = null;
                        cropperInitialized = false;
                        console.log('Cropper cleaned up');
                    } catch (error) {
                        console.error('Error cleaning up cropper:', error);
                    }
                }
            }

            // Initialize cropper function
            function initializeCropper() {
                // Always cleanup first
                cleanupCropper();

                const image = document.getElementById('cropperImage');
                const saveBtn = document.getElementById('saveCropBtn');

                if (!image) {
                    console.log('Cropper image not found');
                    return;
                }

                // Wait for image to load
                function createCropper() {
                    try {
                        cropper = new Cropper(image, {
                            aspectRatio: 1,
                            viewMode: 1,
                            dragMode: 'move',
                            autoCropArea: 0.7,
                            restore: false,
                            guides: true,
                            center: true,
                            highlight: false,
                            cropBoxMovable: true,
                            cropBoxResizable: true,
                            toggleDragModeOnDblclick: false,
                            minCropBoxWidth: 100,
                            minCropBoxHeight: 100,
                            background: true,
                            modal: true,
                            responsive: true,
                            checkOrientation: false,
                            ready: function () {
                                console.log('Cropper initialized successfully');
                                cropperInitialized = true;
                            }
                        });
                    } catch (error) {
                        console.error('Error creating cropper:', error);
                    }
                }

                // Load cropper library if needed
                if (typeof Cropper === 'undefined') {
                    if (!window.cropperLoading) {
                        window.cropperLoading = true;

                        const link = document.createElement('link');
                        link.rel = 'stylesheet';
                        link.href = 'https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css';
                        document.head.appendChild(link);

                        const script = document.createElement('script');
                        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js';
                        script.onload = function() {
                            window.cropperLoading = false;
                            setTimeout(createCropper, 100);
                        };
                        document.head.appendChild(script);
                    }
                } else {
                    createCropper();
                }

                // Setup save button (remove any existing listeners first)
                if (saveBtn) {
                    saveBtn.replaceWith(saveBtn.cloneNode(true)); // Remove all event listeners
                    document.getElementById('saveCropBtn').onclick = function(e) {
                        e.preventDefault();
                        saveCroppedImage();
                    };
                }
            }

            // Save cropped image function
            function saveCroppedImage() {
                if (!cropper || !cropperInitialized) {
                    console.error('Cropper not properly initialized');
                    return;
                }

                try {
                    cropper.getCroppedCanvas({
                        width: 300,
                        height: 300,
                        minWidth: 256,
                        minHeight: 256,
                        maxWidth: 1024,
                        maxHeight: 1024,
                        fillColor: '#fff',
                        imageSmoothingEnabled: false,
                        imageSmoothingQuality: 'high',
                    }).toBlob((blob) => {
                        if (!blob) {
                            console.error('Failed to create blob');
                            return;
                        }

                        const reader = new FileReader();
                        reader.onloadend = () => {
                            if (typeof @this !== 'undefined') {
                                @this.call('saveCroppedImage', reader.result)
                                    .then(() => {
                                        console.log('Image saved successfully');
                                    })
                                    .catch((error) => {
                                        console.error('Error saving image:', error);
                                    });
                            }
                        };
                        reader.readAsDataURL(blob);
                    }, 'image/png', 0.9);
                } catch (error) {
                    console.error('Error in saveCroppedImage:', error);
                }
            }

            // Listen for Livewire updates - This is the key fix
            document.addEventListener('livewire:load', function () {
                Livewire.hook('message.processed', (message, component) => {
                    // Check if crop modal is visible
                    setTimeout(() => {
                        const cropModal = document.querySelector('.crop-modal-overlay');
                        const cropImage = document.getElementById('cropperImage');

                        if (cropModal && cropImage) {
                            console.log('Modal detected, initializing cropper...');
                            initializeCropper();
                        }
                    }, 100);
                });
            });

            // For Livewire v3 compatibility
            if (typeof Livewire !== 'undefined' && Livewire.hook) {
                Livewire.hook('morph.updated', ({ el, component }) => {
                    setTimeout(() => {
                        const cropModal = document.querySelector('.crop-modal-overlay');
                        const cropImage = document.getElementById('cropperImage');

                        if (cropModal && cropImage) {
                            console.log('Modal detected after morph, initializing cropper...');
                            initializeCropper();
                        }
                    }, 100);
                });
            }

            // Auto-hide success message
            window.addEventListener('auto-hide-message', function() {
                setTimeout(() => {
                    const alert = document.getElementById('successAlert');
                    if (alert) {
                        alert.style.transition = 'opacity 0.5s ease';
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            if (typeof @this !== 'undefined') {
                                @this.set('successMessage', null);
                            }
                        }, 500);
                    }
                }, 3000);
            });

            // SweetAlert listeners
            window.addEventListener('swal:success', function(event) {
                if (typeof Swal !== 'undefined') {
                    const data = event.detail[0] || event.detail;
                    Swal.fire({
                        title: data.title,
                        text: data.text,
                        icon: data.icon,
                        confirmButtonText: 'OK'
                    });
                }
            });

            window.addEventListener('swal:error', function(event) {
                if (typeof Swal !== 'undefined') {
                    const data = event.detail[0] || event.detail;
                    Swal.fire({
                        title: data.title,
                        text: data.text,
                        icon: data.icon,
                        confirmButtonText: 'OK'
                    });
                }
            });

            // Clean up on page unload
            window.addEventListener('beforeunload', cleanupCropper);
        });
    </script>
<script>
    function deactivateWithPassword() {
        Swal.fire({
            title: 'Confirm Account Deactivation',
            text: 'Please enter your password to confirm.',
            input: 'password',
            // inputLabel: 'Password',
            inputPlaceholder: 'Enter your password',
            inputAttributes: {
                autocapitalize: 'off',
                autocorrect: 'off',
                autocomplete: 'new-password'
            },
            showCancelButton: true,
            confirmButtonText: 'Deactivate',
            cancelButtonText: 'Cancel',
            showLoaderOnConfirm: true,
            allowOutsideClick: () => !Swal.isLoading(),
            allowEnterKey: false,
            preConfirm: (password) => {
                if (!password) {
                    Swal.showValidationMessage('Password is required');
                    return false;
                }

                // Defer Livewire call to avoid blocking alert
                return new Promise((resolve) => {
                    Livewire.emit('confirmDeactivation', password);
                    resolve();
                });
            }
        });
    }
    </script>

