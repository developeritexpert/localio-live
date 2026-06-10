<div class="col-lg-9 p-0">
    <div class="user_content user_info">
        <div class="uer_nm">
            <h1>My Profile </h1>
        </div>
        <div class="profile-main">
            <div class="profile-main-hd d-flex">
                <div class="profile-img">
                    @if ($profile_image)
                        <img src="{{ asset($profile_image) }}" alt="Profile Image">
                    @else
                    <img src="{{ asset($defaultImage) }}" class="rounded-circle" alt="Default User Image" style="width: 100px; height: 100px; object-fit: cover;">
                    @endif
                    <div class="upload-img">
                        <input type="file" id="fileInput" style="display: none;" wire:model="new_profile_image">
                        <label for="fileInput">
                            <img src="{{ asset('user-dashboard-theme/img/upload-img.svg') }}" alt="Upload">
                        </label>
                    </div>
                </div>
                <button class="blue-btn" wire:click="$dispatch('uploadImage')"
                    onclick="document.getElementById('fileInput').click()">
                    <span wire:loading wire:target="new_profile_image">Uploading...</span>
                    <span wire:loading.remove wire:target="new_profile_image">Change Profile</span>
                </button>
            </div>
            <!-- Personal Data Section -->
            <div class="reward-main-inner">
                <div class="rewrd-inner-hd">
                    <h4>Personal Data</h4>
                </div>
                <div class="rewrd-innr-btm d-flex">
                    <form wire:submit.prevent="updateProfile">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-wrp">
                                    <label>First Name</label> <br>
                                    <input type="text" wire:model.lazy="first_name">
                                    @error('first_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-wrp">
                                    <label>Last Name</label> <br>
                                    <input type="text" wire:model.lazy="last_name">
                                    @error('last_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-wrp">
                                    <label>Phone Number</label> <br>
                                    <input type="number" wire:model.lazy="number">
                                    @error('number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-wrp">
                                    <label>Email</label> <br>
                                    <input type="email" wire:model.lazy="email">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Public Information Section -->
            <div class="reward-main-inner mt-30">
                <div class="rewrd-inner-hd">
                    <h4>Public Information</h4>
                </div>
                <div class="rewrd-innr-btm d-flex">
                    <form wire:submit.prevent="updateProfile">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-wrp">
                                    <label>Company Name</label> <br>
                                    <input type="text" wire:model.lazy="company_name"
                                        placeholder="Enter your company name">
                                    @error('company_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-wrp">
                                    <label>Company Size</label> <br>
                                    <select wire:model.lazy="company_size">
                                        <option value="" disabled selected hidden>-- Select one --</option>
                                        <option value="1-20">1 - 20 Employees</option>
                                        <option value="21-50">21 - 50 Employees</option>
                                        <option value="51-250">51 - 250 Employees</option>
                                        <option value="251-500">251 - 500 Employees</option>
                                        <option value="501-5000">501 - 5000 Employees</option>
                                        <option value="5001">5001 and up Employees</option>
                                    </select>
                                    @error('company_size')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-wrp">
                                    <label>Job Title</label> <br>
                                    <input type="text" wire:model.lazy="job_title"
                                        placeholder="Enter your job title">
                                    @error('job_title')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Save Changes Button -->
            <div class="sve-btn">
                <button class="unq_btn" wire:click="updateProfile"> Save Changes </button>
            </div>
        </div>

        <!-- Deactivate Account Section -->
        <div class="profile-main deactivate-accnt">
            <h6>Deactivate</h6>
            <p>Deactivating your account will disable your profile and remove your name from any content you've
                submitted.</p>
                <a href="javascript:void(0);"
                x-data
                x-on:click="if (confirm('Are you sure you want to deactivate your account?')) { $wire.deactivateAccount2() }">
                Yes, deactivate my account.
             </a>
        </div>
    </div>
</div>
