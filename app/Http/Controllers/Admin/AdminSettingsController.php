<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryProduct;
use App\Models\ProCons;
use App\Models\ProConsTranslation;
use App\Models\Product;
use App\Models\ProductTranslation;
use Illuminate\Http\Request;
use App\Models\WebSetting;
use App\Models\AffiliateClick;
use App\Services\MediaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class AdminSettingsController extends Controller
{
    protected $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function index()
    {
        return view('Admin.setting.dbrefresh.index');
    }

    public function refersh_database(Request $request)
    {
        $request->validate([
            'password' => 'required|min:3'
        ]);
        $pass_check =   Hash::check($request->password, Auth::user()->password);
        if ($pass_check) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            if (Product::count() == 0 && CategoryProduct::count() == 0 && ProductTranslation::count() == 0 && ProCons::count() == 0) {
                return redirect()->route('dbrefresh.index')->with('error', 'Allready Refreshed Data');
            }
            $product_image = Product::pluck('product_image');
            $product_icon = Product::pluck('product_icon');
            $allImages = $product_image->merge($product_icon);

            foreach ($allImages as $image) {
                if (!empty($image)) {
                    $imagePath = public_path('ProductImage/' . $image);
                    $iconPath = public_path('ProductIcon/' . $image);
                    if (File::exists($imagePath) || File::exists($iconPath)) {
                        File::delete($imagePath);
                        File::delete($iconPath);
                    }
                }
            }
            Product::truncate();
            CategoryProduct::truncate();
            ProductTranslation::truncate();
            ProConsTranslation::truncate();
            ProCons::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return redirect()->route('dbrefresh.index')->with('success', 'Refresh the product table Successfully');
        } else {
            return redirect()->route('dbrefresh.index')->with('error    ', 'Please Enter Correct Password !');
        }
    }

    public function edit()
    {

        $data = WebSetting::where('type', 'config')->get(); // or whatever type you're targeting


        return view('Admin.setting.Default_image.update', compact('data'));
    }


    public function addWebSetting(Request $request)
    {
        $type = $request->input('type', 'config');
        $settings = WebSetting::where('type', $type)->get();
    
        foreach ($settings as $setting) {
            $key = $setting->key;
    
            if ($setting->field_type === 'file' && $request->hasFile($key)) {
                $image = $request->file($key);
                $imageName = time() . '_' . $key . '.' . $image->getClientOriginalExtension();
                $dir = 'assets/static';
                $path = "$dir/$imageName";
    
                // Delete old image if it exists
                if (!empty($setting->value) && file_exists(public_path($setting->value))) {
                    @unlink(public_path($setting->value));
                }
    
                // Move new image
                $image->move(public_path($dir), $imageName);
    
                // Update path in DB
                $setting->value = $path;
    
            } elseif ($request->has($key)) {
                $setting->value = $request->input($key);
            }
    
            $setting->save();
        }
    
        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
    
    



    public function updateDefaultImage(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:web_settings,id', // Ensure ID exists in DB
            'name' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'value' => 'nullable|string|max:255',
            'is_active' => 'required|in:0,1',
            'field_type' => 'nullable|string|max:255',
            'user_default_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $setting = WebSetting::findOrFail($request->id);

        $setting->update([
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->value,
            'is_active' => $request->is_active,
            'field_type' => $request->field_type,
        ]);

        if ($request->hasFile('user_default_image')) {
            if ($setting->user_default_image) {
                $this->mediaService->deleteMediaByPath($setting->user_default_image);
            }


            $file = $this->mediaService->uploadMedia($request->file('user_default_image'), 'default/profile/images');
            $filePath = "{$file->dir_path}/{$file->file_name}";

            $setting->update(['user_default_image' => $filePath]);
        }

        return redirect()->back()->with('success', 'Setting updated successfully!');
    }
    // Add to your existing admin controller or create new one
    public function trackingStats()
    {
        $stats = [
            'total_clicks' => AffiliateClick::count(),
            'total_conversions' => AffiliateClick::where('converted', true)->count(),
            'total_revenue' => AffiliateClick::sum('revenue'),
            'conversion_rate' => AffiliateClick::count() > 0 ? 
                round((AffiliateClick::where('converted', true)->count() / AffiliateClick::count()) * 100, 2) : 0
        ];
        
        $recent_conversions = AffiliateClick::with('business')
            ->where('converted', true)
            ->latest('converted_at')
            ->take(10)
            ->get();

        // dd($stats,$recent_conversions);
        
        return view('Admin.ad-tracking.ad_tracking', compact('stats', 'recent_conversions'));
    }

}




