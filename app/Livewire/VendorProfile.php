<?php
namespace App\Livewire;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Illuminate\Http\UploadedFile;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\MetaVendor;
use App\Services\MediaService;
use App\Models\WebSetting;
use Illuminate\Support\Facades\Storage;

class VendorProfile extends Component
{
    use WithFileUploads;

    public $first_name, $last_name, $email, $number, $job_title, $company_name, $company_size;
    public $profile_image, $new_profile_image;
    public $successMessage = '';
    public $uploading = false;
    protected $mediaService;
    public $defaultImage;

    public function mount(MediaService $mediaService)
    {
        $user = Auth::user();
        $vendor = MetaVendor::where('user_id', $user->id)->first();
        $this->mediaService = $mediaService;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->number = $user->number;
        $this->profile_image = $user->profile_image;

        $this->job_title = $vendor->job_title ?? '';
        $this->company_name = $vendor->company_name ?? '';
        $this->company_size = $vendor->company_size ?? '';
        $setting = WebSetting::first();
        $this->defaultImage = $setting ? asset($setting->user_default_image) : asset('default-avatar.png');
    }
    public function updateProfile()
    {
        try {
            $user = Auth::user();
            $this->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'number' => 'nullable|numeric|digits:10',
                'job_title' => 'required|string|max:255',
                'company_name' => 'required|string|max:255',
                'company_size' => 'required|string|max:255',
            ]);
            $user->update([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'number' => $this->number,
            ]);
            MetaVendor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'job_title' => $this->job_title,
                    'company_name' => $this->company_name,
                    'company_size' => $this->company_size,
                ]
            );
            $this->dispatch('swal:success', [
                'title' => 'Profile Updated!',
                'text' => 'Your profile has been successfully updated.',
                'icon' => 'success',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Update Failed!',
                'text' => $e->getMessage(),
                'icon' => 'error',
            ]);
        }
    }

    public function updatedNewProfileImage(MediaService $mediaService)
    {
        $this->uploading = true;
        try {
            // Validate image file
            $this->validate([
                'new_profile_image' => 'image|max:2048',
            ]);
            $user = Auth::user();
            if ($this->new_profile_image) {
                $tempPath = $this->new_profile_image->getRealPath();
                $originalName = $this->new_profile_image->getClientOriginalName();
                $mimeType = $this->new_profile_image->getMimeType();
                $uploadedFile = new UploadedFile($tempPath, $originalName, $mimeType, null, true);
                $media = $mediaService->uploadMedia($uploadedFile, 'vendors/profile/images');
                if ($media) {
                    $profileImagePath = "{$media->dir_path}/{$media->file_name}";
                    $user->update(['profile_image' => $profileImagePath]);
                    $this->profile_image = asset($profileImagePath);
                }
                $this->dispatch('swal:success', [
                    'title' => 'Profile Image!',
                    'text' => 'Your Profile Image updated successfully.',
                    'icon' => 'success',
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Upload Failed!',
                'text' => $e->getMessage(),
                'icon' => 'error',
            ]);
        }
        $this->uploading = false; // Hide uploading state
        $this->reset('new_profile_image'); // Clear file input
    }
    public function deactivateAccount2()
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'Unauthorized action.');
        }
        // Delete profile image manually from public path
        if ($user->profile_image) {
            $imagePath = public_path($user->profile_image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        // Optionally delete related models here (e.g. reviews, listings)
        $user->reviews()->delete();
        $user->wishlists()->delete();
        // $user->listings()->delete();
        Auth::logout();
        // Delete user
        $user->delete();
        session()->flash('success', 'Your account and all related data have been deleted.');
        // Dispatching event for SweetAlert
        $this->dispatch('swal:success', [
            'title' => 'Success',
            'text' => 'Your account and all related data have been deleted.',
            'icon' => 'success',
        ]);
        return redirect()->route('home');
    }
    public function render()
    {
        return view('livewire.vendor-profile');
    }
}
