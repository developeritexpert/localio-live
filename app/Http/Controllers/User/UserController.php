<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Session;
class UserController extends Controller
{
    //
    public function index()
    {
        return view('User.contact.index');
    }
    public function profile($locale = null, $id = null)
    {
        // If the first parameter is an ID, shift them
        if (is_numeric($locale)) {
            $id = $locale;
        } elseif ($id === null) {
            $id = $locale; // In case route params are different
        }
        
        $user = \App\Models\User::findOrFail($id);

        $lang_id = getCurrentLanguageID();

        // Get all published reviews
        $reviews = $user->reviews()
            ->where('reviews.status', 'active')
            ->with([
                'business' => function($q) use ($lang_id) {
                    $q->with(['translations' => fn($t) => $t->where('lang_id', $lang_id)]);
                },
                'translations' => fn($q) => $q->where('language_id', $lang_id)
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get distinct categories reviewed
        $categoryIds = \App\Models\Review::where('user_id', $id)
            ->where('reviews.status', 'active')
            ->join('businesses', 'reviews.business_id', '=', 'businesses.id')
            ->distinct()
            ->pluck('businesses.category_id')
            ->toArray();
        
        $categoriesReviewedCount = count(array_filter($categoryIds));

        // Get the badge using the logic we added to User model
        $badge = $user->getContributorBadge();

        return view('User.user_profile', compact('user', 'reviews', 'categoriesReviewedCount', 'badge'));
    }
}

