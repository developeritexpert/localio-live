<?php

namespace App\Http\Controllers\UserDashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wishlist;
use App\Models\HomeContent;
use Illuminate\Support\Str;
use App\Models\Review;
use App\Models\MailTemplate;
use App\Models\Ticket;
use Illuminate\Support\Facades\Hash;



class UserDashboardController extends Controller
{
    public function userAccount(){

        return view('user_dashboard.user_account');
    }

    public function userProduct()
    {
        $userId = Auth::id(); // Get logged-in user ID
        if (!$userId) {
            return redirect()->route('login')->with('error', 'You need to log in first!');
        }
        $wishlistItems = Wishlist::where('user_id', $userId)
        ->with([
            'business.translations',
            'business.products.prices',
            'business.products.translations',
            'business.reviews.translations'
        ])
        ->get();
        $businessIds = Wishlist::where('user_id', $userId)->pluck('business_id')->toArray();
        $startingprice = getBusinessesWithStartingPrice($businessIds);
            // dd($startingprice);
        return view('user_dashboard.user_product', compact('wishlistItems','startingprice'));
    }
    public function userProfile(){
        return view('user_dashboard.user_profile');
    }

    public function userReview(){
        // $reviews = Review::with(['user', 'business', 'translations'])
        // ->orderBy('created_at', 'desc')
        // ->paginate(5);
        // return view('user_dashboard.user_review',compact('reviews'));

        $userId = auth()->id(); // Get currently logged-in user ID
        $lang_id = getCurrentLanguageID();
        $reviews = Review::with([
            'user',
            'business',
            'translations' => function ($query) use ($lang_id) {
                $query->where('language_id', $lang_id);
            }
        ])
        ->whereHas('translations', function ($query) use ($lang_id) {
            $query->where('language_id', $lang_id);
        })
        ->where('user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->paginate(5);

        return view('user_dashboard.user_review', compact('reviews'));

    }

    public function userReward(){


        return view('user_dashboard.user_reward');
    }

    public function userDeal(){
        $token = session('api_token');
        return view('user_dashboard.user_deals',compact('token'));
    }

    public function userChangePassword(){
        $user=Auth::user();
        return view('user_dashboard.user_change_password',compact('user'));
    }

    public function userEmailPreferences(){
        $user = Auth::user();
        $langId = $user->lang_id ?? 1;

        // Fetch all mail templates and convert them to preference-like array
        $mailTemplates = MailTemplate::where('is_active', true)->get();

        $preferences = $mailTemplates->map(function ($template) use ($user, $langId) {
            $translation = $template->getTranslation($langId);

            return [
                'id' => 'mail_' . $template->id,
                'template_id' => $template->id,
                'icon' => 'fas fa-envelope',
                'title' => $translation['key'] ?? $template->key,
                'desc' => 'You can disable this email if you don’t want to receive it.',
                'value' => !$user->hasDisabledTemplate($template->id),
            ];
        });

        return view('user_dashboard.user_email_preferences',compact('user' ,'preferences'));
    }

    public function updateEmailPreferences(Request $request)
    {
        // dd([
        //     'All Request Data' => $request->all(),
        // ]);

        $request->validate([
            'template_id' => 'required|exists:mail_templates,id',
            'enabled' => 'required|boolean',
        ]);

        $user = auth()->user();
        $templateId = $request->input('template_id');

        if ($request->input('enabled')) {
            $user->disabledMailTemplates()->detach($templateId);
        } else {
            $user->disabledMailTemplates()->syncWithoutDetaching([$templateId]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Updated successfully.',
        ]);
    }



    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'old_password.required'     => 'Please enter your current password.',
            'new_password.required'     => 'Please enter a new password.',
            'new_password.min'          => 'The new password must be at least 8 characters.',
            'new_password.confirmed'    => 'The new password confirmation does not match.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {

            return back()->withErrors(['old_password' => 'The current password you entered is incorrect.']);
        }

        if ($request->old_password === $request->new_password) {
            return back()->withErrors(['new_password' => 'The new password cannot be the same as the current password.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Your password has been updated successfully.');
    }

    public function userSupport(){
        $tickets=Ticket::where('user_id',auth()->user()->id)->whereHas('messages')->with('messages')->get();
        // dd($tickets);

        return view('user_dashboard.user_support',compact('tickets'));
    }

    public function supportView($lang_code,$id){
        // dd($id);
        $ticket=Ticket::where('user_id',auth()->user()->id)->where('ticket_id',$id)->with('messages')->first();
        // dd($ticket);
        return view('user_dashboard.user_support_view',compact('ticket'));
    }

}
