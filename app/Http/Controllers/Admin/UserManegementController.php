<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Language;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
// use App\Models\VendorRegisterList;
class UserManegementController extends Controller
{

    public function allUser()
    {
        $users = User::where('user_type', 'user')->paginate(10);
        return view('Admin.user.all_user', compact('users'));
    }

    // public function allvendor()
    // {
    //     $users = User::where('user_type', 'vendor')->paginate(10);
    //     return view('Admin.user.all_vendor', compact('users'));
    // }

    public function allvendor()
    {
        $users = User::with('business')
            ->where('user_type', 'vendor')
            ->where('status', 'active')   // only approved vendors
            ->paginate(10);

        return view('Admin.user.all_vendor', compact('users'));
    }

    public function editUser(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        return view('Admin.user.edit_user', compact('user'));
    }
    public function editVendor(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        return view('Admin.user.edit_vendor', compact('user'));
    }
    public function statusupdate($id)
    {
        $user = User::findOrFail($id);

        $user->status = $user->status === 'active' ? 'pending' : 'active';
        $user->save();

        if ($user->user_type === 'vendor') {
            return redirect()->route('admin-all-vendors')->with('success', 'Vendor status changed successfully.');
        }

        return redirect()->route('admin-all-user')->with('success', 'User status changed successfully.');
    }

    public function updateVendor(Request $request)
    {

        $lang = Language::where('lang_code', session('lang_code'))->first();
        // Validation rules
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email' . ($request->id ? ',' . $request->id : ''),
            'role'       => 'required|in:vendor',
            'password'   => $request->id ? 'nullable|min:6' : 'required|min:6',
        ];

        // Custom messages (optional)
        $messages = [
            'email.unique' => 'This email is already taken.',
            'password.required' => 'Password is required for new users.',
        ];

        // Run validation
        $validatedData = $request->validate($rules, $messages);

        // Find or create user
        if ($request->id) {
            $user = User::find($request->id);
            if (!$user) {
                return redirect()->back()->withErrors(['User not found']);
            }
        } else {
            $user = new User;
        }

        // Update user fields
        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->email      = $request->email;
        $user->user_type  = $request->role;
        $user->country_id = $lang->id;

        // Set password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin-all-vendors')->with('success', 'User updated successfully.');
    }

    public function updateUser(Request $request)
    {
        $lang = Language::where('lang_code', session('lang_code'))->first();

        // Validation rules
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email' . ($request->id ? ',' . $request->id : ''),
            'role'       => 'required|in:user,admin,vendor',
            'password'   => $request->id ? 'nullable|min:6' : 'required|min:6',
        ];

        // Custom messages (optional)
        $messages = [
            'email.unique' => 'This email is already taken.',
            'password.required' => 'Password is required for new users.',
        ];

        // Run validation
        $validatedData = $request->validate($rules, $messages);

        // Find or create user
        if ($request->id) {
            $user = User::find($request->id);
            if (!$user) {
                return redirect()->back()->withErrors(['User not found']);
            }
        } else {
            $user = new User;
        }

        // Update user fields
        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->email      = $request->email;
        $user->user_type  = $request->role;
        $user->country_id = $lang->id;

        // Set password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin-all-user')->with('success', 'User updated successfully.');
    }
    public function deletevendor($id)
    {
        $user = User::where('id', $id)->first();
        $user->delete();
        return redirect()->route('admin-all-vendors')->with('success', 'Vendor Deleted successfully');
    }

    public function deleteUser($id)
    {
        $user = User::where('id', $id)->first();
        // dd($user);
        $user->delete();
        return redirect()->route('admin-all-user')->with('success', 'User Deleted successfully');
    }


     // Show list of vendor registration requests
    // public function allVendorRegisterRequest()
    // {
    //     $vendors = VendorRegisterList::with('business')->get();

    //     return view('Admin.user.vendor_register_request', compact('vendors'));
    // }

    // // Handle approval or rejection
    // public function handleRegisterRequest($id, $action)
    // {
    //     //validate the action
    //     if(!in_array($action,['approve','reject'])) {
    //         abort(400, 'Invalid action.');
    //     }

    //     //Find the vendor registration request by ID
    //     $vendor = VendorRegisterList::findOrFail($id);

    //     //Update the status field

    //     $vendor->status = $action;
    //     $vendor->save();

    //      // Redirect back with a success message
    // return redirect()->route('allVendorRegisterRequest')->with('success', "Vendor request has been {$action}d.");

    // }



   // Show list of vendor registration requests
    public function allVendorRegisterRequest()
{
    // Show pending vendor requests
    $vendors = User::with('business')
        ->where('user_type', 'vendor')
        ->where('status', 'pending')
        ->get();

    return view('Admin.user.vendor_register_request', compact('vendors'));
}

public function handleRegisterRequest($id, $action)
{
    if (!in_array($action, ['approve', 'reject'])) {
        abort(400, 'Invalid action.');
    }

    $vendor = User::with('business')->findOrFail($id);

    if ($action === 'approve') {
        // Check if this business already has an active vendor
        $exists = User::where('business_id', $vendor->business_id)
            ->where('status', 'active')
            ->where('user_type', 'vendor')
            ->where('id', '!=', $vendor->id)
            ->exists();

        if ($exists) {
            return redirect()->route('allVendorRegisterRequest')
                ->with('error', "The business '{$vendor->business->name}' already has an active vendor.");
        }

        // Approve vendor
        $vendor->status = 'active';  // Approved
    } else {
        // Reject vendor
        $vendor->status = 'rejected';
    }

    $vendor->save();

    return redirect()->route('allVendorRegisterRequest')
        ->with('success', "Vendor request has been {$action}d successfully.");
    }

}






