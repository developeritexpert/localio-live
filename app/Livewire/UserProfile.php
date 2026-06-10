<?php

namespace App\Livewire;

use Illuminate\Support\Facades\File;
use Livewire\Component;
use Illuminate\Http\UploadedFile;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Models\WebSetting;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Services\MediaService;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
class UserProfile extends Component
{
    use WithFileUploads;

    public $first_name, $last_name, $email, $number, $profile_image, $new_profile_image, $country_id;
    public $job_title, $industry, $company_size;
    public $countries = [];

    public $successMessage = '';
    public $uploading = false;
    protected $mediaService;
    public $defaultImage;
    public $showCropModal = false;
    public $temporaryImageUrl;
    public $cropData = [];

    protected $listeners = ['imageCropped' => 'handleCroppedImage'];

    public function mount(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
        $user = Auth::user();

        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->number = $user->number;
        $this->profile_image = $user->profile_image;
        $this->country_id = $user->country_id;
        $this->job_title = $user->job_title;
        $this->industry = $user->industry;
        $this->company_size = $user->company_size;

        $this->countries = \App\Models\Country::orderBy('name')->pluck('name', 'id')->toArray();

        $setting = WebSetting::first();
        $this->defaultImage = $setting ? asset($setting->user_default_image) : asset('default-avatar.png');
    }
    public function updateProfile()
    {
        $user = Auth::user();
    
        $this->email = trim($this->email);
        $this->first_name = trim($this->first_name);
        $this->last_name = trim($this->last_name);
    
        try {
            $this->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'number' => 'nullable|numeric|digits:10',
                'country_id' => 'nullable|exists:countries,id',
                'job_title' => 'nullable|string|max:255',
                'industry' => 'nullable|string|max:255',
                'company_size' => 'nullable|string|max:255',
            ]);
    
            // Success: update user
            $user->update([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'number' => $this->number,
                'country_id' => $this->country_id,
                'job_title' => $this->job_title,
                'industry' => $this->industry,
                'company_size' => $this->company_size,
            ]);
    
            $this->dispatch('inputs:updated'); // Optional
            $this->dispatch('swal:success', [
                'title' => 'Profile Updated!',
                'text' => 'Your profile has been successfully updated.',
                'icon' => 'success',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // ✅ Force UI update on validation fail
            $this->dispatch('inputs:updated');
            throw $e; // re-throw so Livewire still handles the errors
        }
    }
    
    public function updatedNewProfileImage()
    {
        $this->validate([
            'new_profile_image' => 'required|image|max:10240', // Max 10MB
        ]);

        if ($this->new_profile_image) {
            $this->temporaryImageUrl = $this->new_profile_image->temporaryUrl();
            $this->showCropModal = true;
        }
        $this->dispatch('inputs:updated');
    }
    public function handleCroppedImage($base64Image)
    {
        try {
            // Remove base64 header if exists
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));

            // Use proper Intervention v3 syntax
            $manager = new ImageManager(new GdDriver());

            // Read and convert image
            $image = $manager->read($imageData)->toPng();

            // Generate filename and save
            $filename = 'profile_images/' . uniqid() . '.png';
            Storage::disk('public')->put($filename, (string) $image);

            // Delete old image
            if ($this->profile_image && Storage::disk('public')->exists($this->profile_image)) {
                Storage::disk('public')->delete($this->profile_image);
            }
            // Save new image
            $this->profile_image = $filename;

            if (auth()->check()) {
                auth()->user()->update(['profile_image' => $filename]);
            }
            $this->successMessage = 'Profile image updated successfully!';
            $this->closeCropModal();
            $this->dispatch('auto-hide-message');

        } catch (\Exception $e) {
            $this->addError('new_profile_image', 'Image crop failed: ' . $e->getMessage());
            $this->closeCropModal();
        }
    }
    public function saveCroppedImage($base64Image, MediaService $mediaService)
    {
        $this->uploading = true;

        try {
            if (!$base64Image) {
                throw new \Exception('No image data received.');
            }

            $user = Auth::user();
            if (!$user) {
                throw new \Exception('User not authenticated.');
            }

            // Delete old profile image if it exists
            if ($user->profile_image) {
                $oldImagePath = str_replace('storage/', 'public/', $user->profile_image);
                if (Storage::exists($oldImagePath)) {
                    Storage::delete($oldImagePath);
                }
            }

            // Decode base64 image
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));

            if (!$imageData) {
                throw new \Exception('Invalid image data.');
            }

            // Process image with Intervention
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($imageData);

            // Resize if needed and convert to PNG
            $image = $image->resize(300, 300)->toPng();

            // Create a unique filename
            $filename = 'profile_' . $user->id . '_' . time() . '.png';
            $tempFilePath = sys_get_temp_dir() . '/' . $filename;

            // Save image temporarily
            file_put_contents($tempFilePath, (string) $image);

            // Wrap in UploadedFile for MediaService
            $uploadedFile = new UploadedFile(
                $tempFilePath,
                $filename,
                'image/png',
                null,
                true
            );

            // Upload via MediaService
            $media = $mediaService->uploadMedia($uploadedFile, 'users/profile/images');

            // Cleanup temp file
            @unlink($tempFilePath);

            if ($media) {
                $profileImagePath = "{$media->dir_path}/{$media->file_name}";

                // Update user profile image
                $user->update(['profile_image' => $profileImagePath]);

                // Update component property
                $this->profile_image = asset($profileImagePath);

                // Dispatch success event
                $this->dispatch('swal:success', [
                    'title' => 'Success!',
                    'text' => 'Your profile image has been updated successfully.',
                    'icon' => 'success'
                ]);
                $this->dispatch('inputs:updated');
                // Also close the modal
                $this->closeCropModal();


            } else {
                throw new \Exception('Failed to upload image to media service.');
            }

        } catch (\Exception $e) {
            Log::error('Profile image upload failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Upload Failed!',
                'text' => 'Profile image could not be updated: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        } finally {
            $this->uploading = false;
            $this->reset('new_profile_image');
        }
    }

    public function closeCropModal()
    {
        // Simply reset the modal state - let JavaScript handle cropper cleanup
        $this->showCropModal = false;
        $this->temporaryImageUrl = null;

        $this->reset('new_profile_image');
         $this->dispatch('inputs:updated');
        // Force a clean re-render
        $this->dispatch('modal-closed');
    }

    // public function deactivateAccount()
    // {
    //     $user = Auth::user();

    //     if (!$user) {
    //         abort(403, 'Unauthorized action.');
    //     }

    //     // Delete profile image manually from public path
    //     if ($user->profile_image) {
    //         $imagePath = public_path($user->profile_image);
    //         if (file_exists($imagePath)) {
    //             unlink($imagePath);
    //         }
    //     }

    //     // Optionally delete related models here (e.g. reviews, listings)
    //     $user->reviews()->delete();
    //     $user->wishlists()->delete();
    //     // $user->listings()->delete();

    //     Auth::logout();

    //     // Delete user
    //     $user->delete();
    //     session()->flash('success', 'Your account and all related data have been deleted.');

    //     // Dispatching event for SweetAlert
    //     $this->dispatch('swal:success', [
    //         'title' => 'Success',
    //         'text' => 'Your account and all related data have been deleted.',
    //         'icon' => 'success',
    //     ]);


    //     return redirect()->route('home');
    // }

    public function deactivateAccount($password)
    {
        if (!Hash::check($password, auth()->user()->password)) {
            $this->dispatch('swal:error', [
                'text' => 'Incorrect password. Deactivation failed.',
            ]);
            return;
        }
    
        // Deactivate the user
        auth()->user()->update(['status' => 'pending']);
    
        Auth::logout();
    
        // Redirect to login page (or homepage)
        return redirect()->to('/login');
    }
    
    public function render()
    {
        return view('livewire.user-profile');
    }
}
