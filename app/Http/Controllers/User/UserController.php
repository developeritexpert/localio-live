<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Session;

class UserController extends Controller
{
    public function index()
    {
        return view('User.contact.index');
    }

    public function profile($locale = null, $id = null, $slug = null)
    {
        // Handle cases where locale might be omitted in the route
        if (is_numeric($locale)) {
            $slug = $id;
            $id = $locale;
            $locale = app()->getLocale();
        } elseif ($id === null) {
            $id = $locale;
            $locale = app()->getLocale();
        }

        // Decode user ID (handles 6-digit zero-padded "000145", raw "145", or 100k offset)
        $cleanId = ltrim((string) $id, '0');
        $numericId = (int) ($cleanId ?: '0');

        $user = \App\Models\User::find($numericId);
        if (!$user && $numericId > 100000) {
            $user = \App\Models\User::find($numericId - 100000);
        }

        if (!$user) {
            abort(404);
        }

        // Canonical 301 redirect if requested ID is not 6-digit padded or slug doesn't match current display name
        $canonicalId = $user->formatted_id;
        $canonicalSlug = $user->profile_slug;
        if ((string)$id !== $canonicalId || $slug !== $canonicalSlug) {
            return redirect()->to($user->profileUrl($locale), 301);
        }

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
        $categoryIds = \App\Models\Review::where('user_id', $user->id)
            ->where('reviews.status', 'active')
            ->join('businesses', 'reviews.business_id', '=', 'businesses.id')
            ->distinct()
            ->pluck('businesses.category_id')
            ->toArray();

        $categoriesReviewedCount = count(array_filter($categoryIds));

        // Get the contributor badge
        $badge = $user->getContributorBadge();

        return response()
            ->view('User.user_profile', compact('user', 'reviews', 'categoriesReviewedCount', 'badge'))
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }

    public function legacyProfileRedirect($locale = null, $id = null)
    {
        if (is_numeric($locale)) {
            $id = $locale;
            $locale = app()->getLocale();
        }

        $cleanId = ltrim((string) $id, '0');
        $numericId = (int) ($cleanId ?: '0');

        $user = \App\Models\User::find($numericId);
        if (!$user && $numericId > 100000) {
            $user = \App\Models\User::find($numericId - 100000);
        }

        if (!$user) {
            abort(404);
        }

        return redirect()->to($user->profileUrl($locale), 301);
    }
}
