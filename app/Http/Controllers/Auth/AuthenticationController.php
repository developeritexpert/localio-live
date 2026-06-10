<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\MailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\DesignTemplate;
use App\Models\Basket;
use  Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use App\Models\OtpVerification;
use App\Mail\ForgetPasswordMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Country;
use App;
use App\Models\Language;
use App\Models\MetaVendor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;


class AuthenticationController extends Controller
{
    public function index(){

        return view('Authentication.login');

    }
    // public function loginProcc(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required|min:6',
    //     ]);

    //     if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
    //         $user = Auth::user();
    //         $locale = app()->getLocale();

    //         if ($user->user_type === 'admin') {
    //             return redirect()->route('admin_dashboard')->with('success', 'Welcome Admin!');
    //         } elseif ($user->user_type === 'user') {
    //             return redirect()->route('user-dashboard',['locale' => $locale])->with('success', 'Successfully logged in!');
    //         } elseif ($user->user_type === 'vendor') {
    //             return redirect()->route('vendor-overview', ['locale' => $locale])
    //                 ->with('success', 'Welcome, Vendor!');
    //         } else {
    //             Auth::logout();
    //             return redirect()->route('login')->with('loginerror', 'Access Denied!');
    //         }
    //     } else {
    //         return redirect()->route('login')->with('loginerror', 'Invalid email or password.');
    //     }
    // }

    public function loginProcc(Request $request)
    {


    $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:6',
    ]);

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $locale = app()->getLocale();

        if ($user->status === 'pending') {
            Auth::logout();
            return redirect()->back()->with('error', 'Your account has been deactivated.');
        }

            // Create a token once at login and save it to session
            $token = $user->createToken('user_token')->plainTextToken;
            session([
                'api_token' => $token,
                'user_id' => $user->id,
            ]);


            //Store intended URL in session if passed from form
            if ($request->filled('url_intended')) {
                session(['url.intended' => $request->input('url_intended')]);
            }

            // Redirect to intended if set
            if(session()->has('url.intended')) {
                return redirect()->intended()->with('success', 'Successfully logged in!');
            }


        switch ($user->user_type) {
            case 'admin':
                return redirect()->route('admin_dashboard')->with('success', 'Welcome Admin!');
            case 'user':
                return redirect()->route('user-dashboard', ['locale' => session('lang_code', 'en-us')])->with('success-login', 'Successfully logged in!');
            case 'vendor':
                return redirect()->route('vendor-overview', ['locale' => session('lang_code', 'en-us')])->with('success', 'Welcome, Vendor!');
            default:
                Auth::logout();
                return redirect()->route('login')->with('error', 'Invalid email or password');
        }
    }

        return redirect()->back()->with('error', 'Invalid email or password.');
    }

    public function register()
    {
        $countries = Country::all();

        return view('Authentication.register',compact('countries'));
    }
    public function registerProcc(Request $request)
    {

        $validated = $request->validate([
            
            'first_name' => 'required',
            'last_name'  => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            // 'country_id' => 'required',
        ]);

        $country_code =Language::where('lang_code',session('lang_code'))->first();


        if($validated){
            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name  = $request->last_name;
            $user->email      = $request->email;
            $user->password   = Hash::make($request->password);
            $user->country_id    = $country_code->country_id;
            $user->save();
            if ($user) {
                Auth::login($user);


                // Create token and store in session
                $token = $user->createToken('user_token')->plainTextToken;
                session([
                    'api_token' => $token,
                    'user_id' => $user->id,
                ]);
                return redirect()->route('user-dashboard', ['locale' => session('lang_code', 'en-us')])->with('success','Success full Register');
            } else {
                return redirect()->back()->withErrors(['error' => 'Authentication failed']);
            }
        }
    }


    public function logout() {
        Auth::logout();
        Session::flush();
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        return redirect()->route('home')->with('success', "You have logged out successfully");
    }
    public function forgotPassword()
    {
        return view('Authentication.forgot_password');
    }
    public function forgotProcc(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'email' => 'required|email',
        ]);
        $user = User::where('email', $request->email)->first();

        session(['reset_email' => $request->email]);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found');
        }
        $otp = rand(123456, 999999);
        $data['otp'] = $otp;
        $verification = [
            'user_id' => $user->id,
            'otp' => $otp,
            'otp_type' => "forgotpassword",
            'expires_at' => Carbon::now()->addMinutes(5)
        ];
        OtpVerification::updateOrCreate(
            ['user_id' => $user->id, 'otp_type' => 'forgotpassword'],
            $verification
        );
        $template = MailTemplate::getByKey('forget_password', getCurrentLanguageID());

    if ($template) {
        $templateModel = MailTemplate::find($template['id']);
        if ($templateModel) {
            $rendered = $templateModel->renderTemplate(1, $data);
            Log::info('Template test results:', [
                'original_template' => $template,
                'rendered_subject' => $rendered['subject'],
                'rendered_body' => $rendered['body'],
                'otp_in_rendered' => strpos($rendered['body'], $otp) !== false ? 'YES' : 'NO'
            ]);
        }
    }
    // Send email
    Mail::to($request->email)->send(new ForgetPasswordMail($data));
    return redirect('/otp-confirm')->with('success', 'OTP has been sent to your email');
    }
    public function otpConfirm()
    {
        return view('Authentication.get_opt');
    }
    public function optProcc(Request $request)
    {
    //    dd($request->all());
        $request->validate([
            'otp' => 'required|digits:6', // Assuming OTP is a 6-digit number
        ]);

        $otp = $request->otp;

        $verificationExists = OtpVerification::where('otp', $otp)
                                            ->where('expires_at', '>=', Carbon::now()->subMinutes(5))
                                            ->first();

        if (!$verificationExists) {
            return redirect()->back()->with('error', 'Your OTP is not correct or has expired');
        }

        $verificationExists->update([
            'expires_at' => Carbon::now()->subMinutes(5)
        ]);

        return redirect('/new-password')->with('success' ,'Your OTP is confirmed');
    }
    public function newPassword()
    {
        return view('Authentication.new_password');
    }
    public function newPasswordProcc(Request $request)
     {
       $email = session('reset_email');

       $lang = app()->getLocale();

       $request->validate([
            'password' => 'required|confirmed',
        ]);
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error','User not found');
        }

        $password = Hash::make($request->password);
        $remember = $request->has('remember');
        $user->password = $password;
        $user->save();
        if (Auth::attempt(['email' => $email, 'password' => $request->password],$remember)) {
            // If authentication is successful, check user role
        if (Auth::user()->user_type == 'admin') {
            return redirect("/{$lang}/admin-dashboard")->with('success', 'Your new password has been created successfully');
        } elseif (Auth::user()->user_type === 'user') {
            return redirect("/{$lang}/user-dashboard")->with('success', 'Your new password has been created successfully');
            }
        }
        return redirect()->back()->with('error', 'Failed to authenticate user');
    }
    public function redirectToGoogle()
    {
        // return Socialite::driver('google')->redirect();
        return Socialite::driver('google')
        ->scopes(['openid', 'profile', 'email'])
        ->with(['prompt' => 'select_account'])
        ->redirect();

    }

    public function handleGoogleCallback()
    {
        // Get the current locale
        $lang = app()->getLocale();

        try {
            // Retrieve the user information from Google
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            // Redirect back to the login page if authentication fails
            return redirect("/{$lang}/login")->with('error', 'Google authentication failed.'.$e->getMessage());
        }

        // Check if the user already exists in the database
        $existingUser = User::where('email', $googleUser->email)->first();

        if ($existingUser) {
            // Log in the existing user
            Auth::login($existingUser);
        } else {
            // Handle the case where the user does not exist
            list($firstName, $lastName) = explode(' ', $googleUser->name . ' ', 2);

            // Create a new user
            $newUser = User::create([
                'first_name' => trim($firstName), // Use trim to avoid any leading/trailing spaces
                'last_name' => trim($lastName), // Last name may be empty if only one name is given
                'email' => $googleUser->email,
                'user_type' => 'user', // Default user type
                'password' => Hash::make($googleUser->email), // Generate a random password
            ]);

            // Log in the newly created user
            Auth::login($newUser);
        }

        // Get the logged-in user
        $user = auth()->user();

        // Redirect based on user type
        // return $this->redirectUser($user, $lang);

        $redirectUrl = $this->redirectUser($user, $lang);
        return view('Authentication.google_popup_view', [
        'redirectUrl' => $redirectUrl
         ]);

    }

    // Helper function to redirect based on user type

    // private function redirectUser($user, $lang)
    // {
    //     if ($user->user_type === 'admin') {
    //         return redirect("/{$lang}/admin-dashboard")->with('success', 'Successfully logged in! Welcome, Admin.');
    //     } elseif ($user->user_type === 'user') {
    //         return redirect("/{$lang}/user-dashboard")->with('success', 'Successfully logged in.');
    //     } else {
    //         abort(404); // Handle unexpected user types
    //     }
    // }


    private function redirectUser($user, $lang)
    {
        if ($user->user_type === 'admin') {
            return "/{$lang}/admin-dashboard";
        } elseif ($user->user_type === 'user') {
            return "/{$lang}/user-dashboard";
        } else {
            abort(404);
        }
    }


    // neeraj code login with facebook
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }
    public function handleFacebookCallback()
    {
        // Get the current locale
        $lang = app()->getLocale();

        try {
            // Retrieve the user information from Facebook
            $facebookUser = Socialite::driver('facebook')->user();
        } catch (\Exception $e) {
            // Log the exception message
            \Log::error('Facebook authentication error: ' . $e->getMessage());

            // Redirect back to the login page if authentication fails
            return redirect("/{$lang}/login")->with('error', 'Facebook authentication failed.');
        }

        // Log the returned user data for debugging
        \Log::info('Facebook User Data:', (array) $facebookUser);

        // Check if the user object is not null and has the necessary properties
        if (is_null($facebookUser) || !property_exists($facebookUser, 'email')) {
            return redirect("/{$lang}/login")->with('error', 'Unable to retrieve user data from Facebook.');
        }

        // Check if the user already exists in the database
        $existingUser = User::where('email', $facebookUser->email)->first();

        if ($existingUser) {
            // Log in the existing user
            Auth::login($existingUser);
        } else {
            // Create a new user if they do not exist
            list($firstName, $lastName) = explode(' ', $facebookUser->name . ' ', 2);
            $newUser = User::create([
                'first_name' => trim($firstName), // Use trim to avoid any leading/trailing spaces
                'last_name' => trim($lastName), // Last name may be empty if only one name is given
                'email' => $facebookUser->email,
                'user_type' => 'user', // Default user type
                'password' => Hash::make($facebookUser->email), // Generate a random password
            ]);

            // Log in the newly created user
            Auth::login($newUser);
        }

        // Get the logged-in user
        $user = auth()->user();

        // Redirect based on user type
        return $this->redirectUserRole($user, $lang);
    }

    // Helper function to redirect based on user type
    private function redirectUserRole($user, $lang)
    {
        if ($user->user_type === 'admin') {
            return redirect("/{$lang}/admin-dashboard")->with('success', 'Successfully logged in! Welcome, Admin.');
        } elseif ($user->user_type === 'user') {
            return redirect("/{$lang}/user-dashboard")->with('success', 'Successfully logged in.');
        } else {
            abort(404); // Handle unexpected user types
        }
    }



    //******************* Vendor Registration functions ********************//

    // public function vendorRegisterForm()
    // {
    //     $countries = Country::all();

    //     return view('Authentication.vendor_register',compact('countries'));
    // }

    // public function vendorRegisterProcess(Request $request)
    // {
    //     $lang = app()->getLocale();
    //     // Validate incoming data
    //     $request->validate([
    //         'first_name'    => 'required',
    //         'last_name'     => 'required',
    //         'job_title'     => 'required',
    //         'business_email'=> 'required|email',
    //         'password' => 'required|min:6',
    //         'business_phone'=> 'required|digits:10',
    //         'country_id'    => 'required',
    //         'company_name'  => 'required',
    //         'company_size'  => 'required',
    //     ]);

    //     // Create and save the User record
    //     $user = new User();
    //     $user->first_name = $request->first_name;
    //     $user->last_name  = $request->last_name;
    //     $user->email      = $request->business_email;
    //     $user->password   = Hash::make($request->password);
    //     $user->number     = $request->business_phone;
    //     $user->country_id = $request->country_id;
    //     $user->user_type  = 'vendor'; // Assign the user type as 'vendor'
    //     $user->save();

    //     // Create and save the MetaVendor record, associate with the created User
    //     $vendor = new MetaVendor();
    //     $vendor->user_id       = $user->id; // Fix this assignment
    //     $vendor->job_title     = $request->job_title;
    //     $vendor->company_name  = $request->company_name; // Fixed the typo in company_name
    //     $vendor->company_size  = $request->company_size;
    //     $vendor->product_name  = $request->product_name ?? null;
    //     $vendor->product_url   = $request->product_url ?? null;
    //     $vendor->website_url   = $request->website_url ?? null;
    //     $vendor->save();
    //     Auth::login($user);
    //     $locale = app()->getLocale(); // Get the locale

    //     return redirect()->route('vendor-overview', ['locale' => $locale])
    //         ->with('success', 'Registration successfully done');
    // }



}
