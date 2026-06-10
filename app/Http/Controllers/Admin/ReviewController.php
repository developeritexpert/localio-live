<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Review;
use App\Models\Language;
use App\Models\ReviewTranslation;

class ReviewController extends Controller
{
    public function reviews()
    {

        $reviews = Review::with(['user', 'business.translations', 'translations'])
            ->where('status','active')
            ->whereHas('business')
            ->whereHas('business.translations')
            ->whereHas('translations')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('Admin.reviews.index', compact('reviews'));
    }

    public function unpublishedReviews(){
            $reviews = Review::with(['user', 'business.translations', 'translations'])
            ->where('status','inactive')
            ->whereHas('business')
            ->whereHas('business.translations')
            ->whereHas('translations')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('Admin.reviews.index', compact('reviews'));
    }

    public function reviewAdd()
    {
        $lang_id = getCurrentLanguageID();
        $businesses = Business::with(['translations' => function ($query) use ($lang_id) {
            $query->where('lang_id', $lang_id)->where('status', 1);
        }])->get();
        return view('Admin.reviews.add_review', compact('businesses'));
    }

    public function reviewAddProc(Request $request)
    {
        $lang_id = getCurrentLanguageID();

        // Validate the request data
        $request->validate([
            'rating'       => 'required|integer|min:1|max:5',
            'description'  => 'required|string|max:2000',
            'title'  => 'required|string|max:2000',
            'business_id'   => 'required|exists:businesses,id',
            //  'public_name' => 'nullable|string',
            'status' => 'nullable',
        ]);

        // Check if user has already reviewed this product
        $existingReview = Review::where('business_id', $request->business_id)
            ->where('user_id', auth()->id())
            ->first();

        // If review exists, handle update
        if ($existingReview) {
            $existingReview->rating = $request->rating;
            $existingReview->status = $request->input('status', 'active');
            // $existingReview->public_name = $request->public_name;
            $existingReview->save();

            // Update translation
            $reviewTranslation = ReviewTranslation::where('reviews_id', $existingReview->id)
                ->where('language_id', $lang_id)
                ->first();

            if ($reviewTranslation) {
                $reviewTranslation->description = $request->description;
                $reviewTranslation->title = $request->title;
                $reviewTranslation->save();
            } else {
                $newTranslation = new ReviewTranslation();
                $newTranslation->reviews_id = $existingReview->id;
                $newTranslation->title = $request->title;
                $newTranslation->description = $request->description;
                $newTranslation->language_id = $lang_id;
                $newTranslation->save();
            }

            return redirect()->route('reviews')->with('success', 'Your review has been updated!');
        }

        // Create a new Review if it doesn't exist
        $review = new Review();
        $review->rating = $request->rating;
        $review->business_id = $request->business_id;
        $review->user_id = auth()->user()->id;
        $review->lang_id = $lang_id;
        $review->status = $request->input('status', 'active');
        // $review->public_name = $request->public_name;
        $review->save();

        // Create ReviewTranslation entry
        $reviewTranslation = new ReviewTranslation();
        $reviewTranslation->reviews_id = $review->id;
        $reviewTranslation->description = $request->description;
        $reviewTranslation->title = $request->title;
        $reviewTranslation->language_id = $lang_id;
        $reviewTranslation->save();

        return redirect()->route('reviews')->with('success', 'Thank you! Your review has been submitted.');
    }


    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        // Check if the current user owns this review
        if ($review->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to delete this review.');
        }
        ReviewTranslation::where('reviews_id', $review->id)->delete();
        $review->delete();

        return redirect()->back()->with('success', 'Your review has been deleted.');
    }
    public function reviewStatusUpdate(Request $request)
    {
        $id = $request->id;
        $review = Review::find($id);

        if (!$review) {
            return redirect()->back()->with(['error' => 'Review not found']);
        }

        // Check the current status and toggle between 'active' and 'inactive'
        $newStatus = $review->status == 'active' ? 'inactive' : 'active';

        // Update the status
        $review->update([
            'status' => $newStatus
        ]);

        return redirect()->back()->with(['success' => 'Review status updated successfully']);
    }

    // public function getLanguage($string){
    //     $string = "United States - English";
    //     $parts = explode(" - ", $string);
    //     $language = $parts[1];
    // }

    public function reviewEdit($id)
    {
        $review = Review::with('business')->findOrFail($id);
        $language = Language::where('lang_code', getCurrentLocale())->first();
        $langId = $language->id;
    //    dd($language);
        $reviewTranslation = ReviewTranslation::where('reviews_id', $review->id)
            ->where('language_id', $langId)
            ->first();
        $businesses = Business::with(['translations' => function ($query) use ($langId) {
            $query->where('lang_id', $langId)->where('status', 1);
        }])->get();
        $defaultTranslation = ReviewTranslation::where('reviews_id', $review->id)
            ->where('language_id', 1) // lang_id = 1 for default language (en-us)
            ->first();
        return view('Admin.reviews.update_review', compact('language','review', 'reviewTranslation', 'businesses', 'defaultTranslation'));
    }
    public function reviewUpdate(Request $request, $id)
    {
        // dd($request->all());
        // Ensure this function returns a valid locale code
        $locale = getCurrentLocale(); // Ensure this function returns a valid locale code
        //dd($locale);
        // Validate the form data
        $request->validate([
            // 'rating' => 'required|integer|min:1|max:5',
            'ease_of_use_rating'=> 'required|integer|min:1|max:5',
            'value_for_money_rating'=> 'required|integer|min:1|max:5',
            'customer_service_rating'=> 'required|integer|min:1|max:5',
            'exclusive_service_rating'=> 'required|integer|min:1|max:5',
            'description'  => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            // 'public_name' => 'nullable|string',

        ]);

        // Get the language record from the database based on the current locale
        $language = Language::where('lang_code', $locale)->first();
        //dd($language);
        // If the language doesn't exist, return an error
        if (!$language) {
            return redirect()->back()->with('error', 'Language not found!');
        }

        // Get the language ID
        $langId = $language->id;
        //dd($langId);

        // Find the review by ID
        $review = Review::findOrFail($id);

        // Update review data

        $review->ease_of_use_rating = $request->ease_of_use_rating;
        $review->value_for_money_rating = $request->value_for_money_rating;
        $review->customer_service_rating = $request->customer_service_rating;
        $review->exclusive_service_rating = $request->exclusive_service_rating;

        $avg_rating = round((

            $request->ease_of_use_rating +
            $request->value_for_money_rating +
            $request->customer_service_rating +
            $request->exclusive_service_rating
        ) / 4, 2);
        $review->rating = $avg_rating;
        $review->status = $request->status;
        // $review->public_name = $request->public_name;
        $review->save();

        // Update or create ReviewTranslation
        $reviewTranslation = ReviewTranslation::where('reviews_id', $review->id)
            ->where('language_id', $langId)
            ->first();

        $reviewTranslation = ReviewTranslation::updateOrCreate(
            ['reviews_id' => $review->id, 'language_id' => $langId],
            [
                'description' => $request->description,
            ]
        );

        return redirect()->route('reviews')->with('success', 'Review updated successfully!');
    }


    public function reviewDelete($id)
    {
        // Find the review by ID
        $review = Review::find($id);

        // Check if the review exists
        if (!$review) {
            return redirect()->back()->with(['error' => 'Review not found']);
        }
        ReviewTranslation::where('reviews_id', $review->id)->delete();

        // Delete the review
        $review->delete();

        // Redirect with success message
        return redirect()->back()->with(['success' => 'Review deleted successfully']);
    }
}
