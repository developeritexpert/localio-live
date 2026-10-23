<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Language;
use App\Models\ModerationCase;
use App\Models\Review;
use App\Models\ReviewAppeal;
use App\Models\ReviewModerationLog;
use App\Models\ReviewReport;
use App\Models\ReviewTranslation;
use App\Models\User;
use App\Models\WebSetting;
use App\Services\MailService;
use App\Services\ReviewModerationService;
use App\Services\ReviewTranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AiBatchJob;
use App\Console\Commands\PollReviewModerationBatch;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;


class ReviewController extends Controller
{
    // --- 1. ALL REVIEWS (Main directory with advanced filters) -------------------
    public function reviews(Request $request)
    {
        $query = Review::with(['user', 'business.translations', 'translations', 'reports'])
            ->withCount('reports');

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Escalation Source Filter
        if ($request->filled('escalation_source')) {
            $query->where('escalation_source', $request->escalation_source);
        }

        // AI Flag Filter
        if ($request->filled('ai_flag')) {
            $flag = $request->ai_flag;
            $query->where(function ($q) use ($flag) {
                $q->whereJsonContains('localio_ai_flags', $flag)
                  ->orWhere('localio_ai_flags', 'like', "%{$flag}%");
            });
        }

        // Search Filter
        if ($request->filled('search')) {
            $search = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('first_name', 'like', $search)
                       ->orWhere('last_name', 'like', $search)
                       ->orWhere('email', 'like', $search);
                })
                ->orWhereHas('business.translations', function ($bq) use ($search) {
                    $bq->where('name', 'like', $search);
                })
                ->orWhereHas('translations', function ($tq) use ($search) {
                    $tq->where('title', 'like', $search)
                       ->orWhere('description', 'like', $search);
                });
            });
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        $stats = [
            'total'          => Review::count(),
            'processing'     => Review::where('status', Review::STATUS_PROCESSING)->count(),
            'published'      => Review::whereIn('status', [Review::STATUS_PUBLISHED, 'active'])->count(),
            'pending_review' => Review::where('status', Review::STATUS_PENDING_REVIEW)->count(),
            'rejected'       => Review::where('status', Review::STATUS_REJECTED)->count(),
            'removed'        => Review::where('status', Review::STATUS_REMOVED)->count(),
        ];

        return view('Admin.reviews.index', compact('reviews', 'stats'));
    }

    // --- 2. PENDING REVIEW QUEUE (Moderator Working Queue) ------------------------
    public function pending(Request $request)
    {
        $query = Review::with(['user', 'business.translations', 'translations', 'reports', 'moderationLogs'])
            ->where('status', Review::STATUS_PENDING_REVIEW);

        // Sorting by Priority (Risk Level: CRITICAL > HIGH > MEDIUM > LOW) and Age
        $query->orderByRaw("
            CASE localio_ai_risk_level
                WHEN 'CRITICAL' THEN 1
                WHEN 'HIGH' THEN 2
                WHEN 'MEDIUM' THEN 3
                WHEN 'LOW' THEN 4
                ELSE 5
            END ASC
        ")->orderBy('created_at', 'asc');

        $reviews = $query->paginate(20);
        $supportedLocales = config('openai.supported_locales', []);

        return view('Admin.reviews.pending', compact('reviews', 'supportedLocales'));
    }

    // --- 3. REPORTED REVIEWS QUEUE -----------------------------------------------
    public function reported(Request $request)
    {
        $reports = ReviewReport::with(['review.business.translations', 'review.user', 'review.translations', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('Admin.reviews.reported', compact('reports'));
    }

    // --- 4. REMOVED & REJECTED REVIEWS (Permanent Audit Trail) -------------------
    public function removed(Request $request)
    {
        $reviews = Review::with(['user', 'business.translations', 'translations', 'reports', 'moderationLogs.actor', 'appeals'])
            ->whereIn('status', [Review::STATUS_REMOVED, Review::STATUS_REJECTED])
            ->orderBy('updated_at', 'desc')
            ->paginate(25);

        return view('Admin.reviews.removed', compact('reviews'));
    }

    // --- 5. REVIEW APPEALS QUEUE -------------------------------------------------
    public function appeals(Request $request)
    {
        $appeals = ReviewAppeal::with(['review.business.translations', 'review.translations', 'user', 'moderator'])
            ->orderByRaw("CASE status WHEN 'open' THEN 1 WHEN 'under_review' THEN 2 ELSE 3 END")
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('Admin.reviews.appeals', compact('appeals'));
    }

    // --- 6. MODERATION CASES (Clustered Coordinated Manipulation) -----------------
    public function cases(Request $request)
    {
        $cases = ModerationCase::with(['business.translations', 'assignee', 'reviews.user', 'reviews.translations'])
            ->withCount('reviews')
            ->orderByRaw("CASE status WHEN 'open' THEN 1 WHEN 'investigating' THEN 2 ELSE 3 END")
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('Admin.reviews.cases', compact('cases'));
    }

    // --- 7. MODERATION SETTINGS --------------------------------------------------
    public function settings()
    {
        $settings = [
            'moderation_model'        => config('openai.moderation_model', 'omni-moderation-latest'),
            'review_ai_model'         => config('openai.review_ai_model', 'gpt-5.6-luna'),
            'review_prompt_id'        => config('openai.review_prompt_id', ''),
            'review_prompt_version'   => config('openai.review_prompt_version', '1'),
            'review_reasoning_effort' => config('openai.review_reasoning_effort', 'low'),
            'review_batch_hours'      => config('openai.review_batch_hours', 12),
            'supported_locales'       => config('openai.supported_locales', []),
        ];

        return view('Admin.reviews.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'review_prompt_id'        => 'required|string',
            'review_prompt_version'   => 'required|string',
            'review_reasoning_effort' => 'required|in:low,medium,high',
            'review_batch_hours'      => 'required|integer|min:1|max:48',
        ]);

        WebSetting::updateOrCreate(['key' => 'OPENAI_REVIEW_PROMPT_ID'], ['value' => $request->review_prompt_id, 'name' => 'Review Prompt ID', 'status' => 1]);
        WebSetting::updateOrCreate(['key' => 'OPENAI_REVIEW_PROMPT_VERSION'], ['value' => $request->review_prompt_version, 'name' => 'Review Prompt Version', 'status' => 1]);
        WebSetting::updateOrCreate(['key' => 'OPENAI_REVIEW_REASONING_EFFORT'], ['value' => $request->review_reasoning_effort, 'name' => 'Reasoning Effort', 'status' => 1]);
        WebSetting::updateOrCreate(['key' => 'REVIEW_MODERATION_BATCH_HOURS'], ['value' => (string)$request->review_batch_hours, 'name' => 'Batch Hours', 'status' => 1]);

        return redirect()->back()->with('success', 'Moderation settings updated successfully.');
    }

    // --- MODERATOR ACTIONS --------------------------------------------------------

    /**
     * Approve review and publish it.
     */
    public function approve(Request $request, $id)
    {
        $review = Review::with(['user', 'business.translations'])->findOrFail($id);
        $fromStatus = $review->status;

        // If locale assignment was requested, update source_locale
        if ($request->filled('assigned_locale')) {
            $assignedLocale = ReviewModerationService::normalizeLocale($request->assigned_locale);
            $review->source_locale = $assignedLocale;
            $review->locale_status = Review::LOCALE_MATCH;
        } elseif (!$review->source_locale) {
            $review->source_locale = $review->submitted_locale ?: 'en-US';
        }

        $review->status = Review::STATUS_PUBLISHED;
        $review->save();

        \App\Services\ReviewTranslationService::queueTranslationsForReview($review);

        ReviewModerationLog::logModerator(
            $review->id,
            ReviewModerationLog::ACTION_MODERATED,
            auth()->id(),
            $fromStatus,
            Review::STATUS_PUBLISHED,
            ['decision' => 'approved', 'notes' => $request->input('notes')]
        );

        // Notify reviewer
        $bizName = $review->business?->translations?->first()?->name ?? 'Business';
        $bizSlug = $review->business?->translations?->first()?->slug ?? '';
        $reviewUrl = $bizSlug ? route('user.product_detail', ['locale' => app()->getLocale() ?? 'en-us', 'id' => $bizSlug]) : url('/');

        if ($review->user && $review->user->email) {
            MailService::sendTemplate('review_approved', $review->user->email, [
                'user_name'     => $review->user->first_name ?? 'User',
                'business_name' => $bizName,
                'review_url'    => $reviewUrl,
            ], $review->user->country_id ?? 1, $review->user);
        }

        return redirect()->back()->with('success', "Review #{$review->id} approved and published successfully.");
    }

    /**
     * Reject review (for newly submitted reviews that were never published).
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $review = Review::with(['user', 'business.translations'])->findOrFail($id);
        $fromStatus = $review->status;

        $review->status = Review::STATUS_REJECTED;
        $review->save();

        \App\Services\ReviewTranslationService::handleRemovedOrRejectedReview($review);

        ReviewModerationLog::logModerator(
            $review->id,
            ReviewModerationLog::ACTION_MODERATED,
            auth()->id(),
            $fromStatus,
            Review::STATUS_REJECTED,
            ['decision' => 'rejected', 'reason' => $request->reason]
        );

        if ($review->user && $review->user->email) {
            $bizName = $review->business?->translations?->first()?->name ?? 'Business';
            MailService::sendTemplate('review_rejected', $review->user->email, [
                'user_name'     => $review->user->first_name ?? 'User',
                'business_name' => $bizName,
                'reason'        => $request->reason,
            ], $review->user->country_id ?? 1, $review->user);
        }

        return redirect()->back()->with('success', "Review #{$review->id} rejected.");
    }

    /**
     * Remove review (for previously published reviews following a report or moderation).
     */
    public function remove(Request $request, $id)
    {
        $request->validate([
            'removal_reason' => 'required|string|max:500',
        ]);

        $review = Review::with(['user', 'business.translations'])->findOrFail($id);
        $fromStatus = $review->status;

        $review->status = Review::STATUS_REMOVED;
        $review->save();

        \App\Services\ReviewTranslationService::handleRemovedOrRejectedReview($review);

        ReviewModerationLog::logModerator(
            $review->id,
            ReviewModerationLog::ACTION_MODERATED,
            auth()->id(),
            $fromStatus,
            Review::STATUS_REMOVED,
            ['decision' => 'removed', 'removal_reason' => $request->removal_reason]
        );

        if ($review->user && $review->user->email) {
            $bizName = $review->business?->translations?->first()?->name ?? 'Business';
            MailService::sendTemplate('review_rejected', $review->user->email, [
                'user_name'     => $review->user->first_name ?? 'User',
                'business_name' => $bizName,
                'reason'        => $request->removal_reason,
            ], $review->user->country_id ?? 1, $review->user);
        }

        return redirect()->back()->with('success', "Review #{$review->id} removed from public visibility.");
    }

    /**
     * Assign / update locale of a review.
     */
    public function assignLocale(Request $request, $id)
    {
        $request->validate([
            'locale' => 'required|string',
        ]);

        $review = Review::findOrFail($id);
        $normalized = ReviewModerationService::normalizeLocale($request->locale);

        $review->source_locale = $normalized;
        $review->locale_status = Review::LOCALE_MATCH;
        $review->save();

        ReviewModerationLog::logModerator(
            $review->id,
            ReviewModerationLog::ACTION_MODERATED,
            auth()->id(),
            $review->status,
            $review->status,
            ['assigned_locale' => $normalized]
        );

        return redirect()->back()->with('success', "Locale for review #{$review->id} set to {$normalized}.");
    }

    /**
     * Handle reviewer appeal resolution.
     */
    public function handleAppeal(Request $request, $id)
    {
        $request->validate([
            'decision' => 'required|in:restore,confirm_rejection,request_info',
            'notes'    => 'nullable|string|max:1000',
        ]);

        $appeal = ReviewAppeal::with('review')->findOrFail($id);
        $review = $appeal->review;

        if ($request->decision === 'restore') {
            $fromStatus = $review->status;
            $review->status = Review::STATUS_PUBLISHED;
            $review->appeal_status = ReviewAppeal::STATUS_RESOLVED;
            $review->save();

            \App\Services\ReviewTranslationService::queueTranslationsForReview($review);

            $appeal->update([
                'status'              => ReviewAppeal::STATUS_RESOLVED,
                'moderator_id'        => auth()->id(),
                'moderator_decision'  => ReviewAppeal::DECISION_RESTORED,
                'moderator_notes'     => $request->notes,
                'resolved_at'         => now(),
            ]);

            ReviewModerationLog::logModerator(
                $review->id,
                ReviewModerationLog::ACTION_APPEALED,
                auth()->id(),
                $fromStatus,
                Review::STATUS_PUBLISHED,
                ['appeal_id' => $appeal->id, 'decision' => 'restored', 'notes' => $request->notes]
            );
        } elseif ($request->decision === 'confirm_rejection') {
            $appeal->update([
                'status'              => ReviewAppeal::STATUS_RESOLVED,
                'moderator_id'        => auth()->id(),
                'moderator_decision'  => ReviewAppeal::DECISION_CONFIRMED_REJECTION,
                'moderator_notes'     => $request->notes,
                'resolved_at'         => now(),
            ]);

            $review->update(['appeal_status' => ReviewAppeal::STATUS_RESOLVED]);

            ReviewModerationLog::logModerator(
                $review->id,
                ReviewModerationLog::ACTION_APPEALED,
                auth()->id(),
                $review->status,
                $review->status,
                ['appeal_id' => $appeal->id, 'decision' => 'confirmed_rejection', 'notes' => $request->notes]
            );
        } else {
            $appeal->update([
                'status'              => ReviewAppeal::STATUS_UNDER_REVIEW,
                'moderator_id'        => auth()->id(),
                'moderator_decision'  => ReviewAppeal::DECISION_INFO_REQUESTED,
                'moderator_notes'     => $request->notes,
            ]);

            $review->update(['appeal_status' => ReviewAppeal::STATUS_UNDER_REVIEW]);
        }

        return redirect()->back()->with('success', 'Appeal decision recorded successfully.');
    }

    /**
     * Backward-compatible legacy toggle for existing admin index button.
     */
    public function reviewStatusUpdate(Request $request, $id = null)
    {
        $id = $id ?? $request->id;
        $review = Review::with(['user', 'business.translations'])->find($id);

        if (!$review) {
            return redirect()->back()->with(['error' => 'Review not found']);
        }

        $fromStatus = $review->status;
        $newStatus = in_array($review->status, [Review::STATUS_PUBLISHED, 'active'])
            ? Review::STATUS_PENDING_REVIEW
            : Review::STATUS_PUBLISHED;

        $review->update(['status' => $newStatus]);

        ReviewModerationLog::logModerator(
            $review->id,
            ReviewModerationLog::ACTION_STATUS_CHANGED,
            auth()->id(),
            $fromStatus,
            $newStatus
        );

        return redirect()->back()->with(['success' => 'Review status updated successfully']);
    }

    public function reviewEdit($id)
    {
        $review = Review::with(['user', 'business.translations', 'translations'])->find($id);

        if (!$review) {
            return redirect()->route('reviews')->with('error', 'Review not found.');
        }

        $locale = function_exists('getCurrentLocale') ? getCurrentLocale() : 'en-us';
        $language = Language::where('lang_code', $locale)->first() ?? Language::find(1);
        if (!$language) {
            $language = (object)['id' => 1, 'name' => 'English', 'lang_code' => 'en-us'];
        }
        $langId = $language->id ?? 1;

        $reviewTranslation = ReviewTranslation::where(function ($q) use ($review) {
                $q->where('reviews_id', $review->id)->orWhere('review_id', $review->id);
            })
            ->where('language_id', $langId)
            ->first();

        $businesses = Business::with(['translations' => function ($query) use ($langId) {
            $query->where('lang_id', $langId)->where('status', 1);
        }])->get();

        $defaultTranslation = ReviewTranslation::where(function ($q) use ($review) {
                $q->where('reviews_id', $review->id)->orWhere('review_id', $review->id);
            })
            ->where('language_id', 1)
            ->first() ?? $reviewTranslation ?? $review->translations->first();

        return view('Admin.reviews.update_review', compact('language', 'review', 'reviewTranslation', 'businesses', 'defaultTranslation'));
    }

    public function reviewUpdate(Request $request, $id)
    {
        $review = Review::with(['user', 'business.translations', 'translations'])->find($id);

        if (!$review) {
            return redirect()->route('reviews')->with('error', 'Review not found.');
        }

        $request->validate([
            'ease_of_use_rating'       => 'required|numeric|min:1|max:5',
            'value_for_money_rating'   => 'required|numeric|min:1|max:5',
            'customer_service_rating'  => 'required|numeric|min:1|max:5',
            'exclusive_service_rating' => 'required|numeric|min:1|max:5',
            'description'              => 'required|string|max:5000',
            'status'                   => 'required|string',
            'business_id'              => 'nullable',
        ]);

        $locale = function_exists('getCurrentLocale') ? getCurrentLocale() : 'en-us';
        $language = Language::where('lang_code', $locale)->first() ?? Language::find(1);
        $langId = $language ? $language->id : 1;

        $fromStatus = $review->status;

        $review->ease_of_use_rating = (int)$request->ease_of_use_rating;
        $review->value_for_money_rating = (int)$request->value_for_money_rating;
        $review->customer_service_rating = (int)$request->customer_service_rating;
        $review->exclusive_service_rating = (int)$request->exclusive_service_rating;

        $avg_rating = round((
            $review->ease_of_use_rating +
            $review->value_for_money_rating +
            $review->customer_service_rating +
            $review->exclusive_service_rating
        ) / 4, 2);
        $review->rating = $avg_rating;

        if ($request->filled('business_id') && Business::where('id', $request->business_id)->exists()) {
            $review->business_id = $request->business_id;
        }

        // Map submitted status
        if (in_array(strtolower($request->status), ['active', Review::STATUS_PUBLISHED])) {
            $review->status = Review::STATUS_PUBLISHED;
        } else {
            $review->status = Review::STATUS_PENDING_REVIEW;
        }

        $review->save();

        // Update or create ReviewTranslation for target language
        $reviewTranslation = ReviewTranslation::where(function ($q) use ($review) {
                $q->where('reviews_id', $review->id)->orWhere('review_id', $review->id);
            })
            ->where('language_id', $langId)
            ->first();

        if ($reviewTranslation) {
            $reviewTranslation->update([
                'description' => $request->description,
            ]);
        } else {
            ReviewTranslation::create([
                'reviews_id'  => $review->id,
                'review_id'   => $review->id,
                'language_id' => $langId,
                'description' => $request->description,
                'status'      => ReviewTranslation::STATUS_ORIGINAL,
            ]);
        }

        // Also update default translation (language_id = 1) if different
        if ($langId != 1) {
            $defaultTranslation = ReviewTranslation::where(function ($q) use ($review) {
                    $q->where('reviews_id', $review->id)->orWhere('review_id', $review->id);
                })
                ->where('language_id', 1)
                ->first();
            if ($defaultTranslation) {
                $defaultTranslation->update([
                    'description' => $request->description,
                ]);
            } else {
                ReviewTranslation::create([
                    'reviews_id'  => $review->id,
                    'review_id'   => $review->id,
                    'language_id' => 1,
                    'description' => $request->description,
                    'status'      => ReviewTranslation::STATUS_ORIGINAL,
                ]);
            }
        }

        // Invalidate translations because review text/ratings were modified
        ReviewTranslationService::invalidateTranslationsForReview($review);

        // If published, queue re-translations
        if (in_array($review->status, [Review::STATUS_PUBLISHED, 'active'])) {
            ReviewTranslationService::queueTranslationsForReview($review);
        }

        ReviewModerationLog::logModerator(
            $review->id,
            ReviewModerationLog::ACTION_MODERATED,
            auth()->id(),
            $fromStatus,
            $review->status,
            ['action' => 'review_edited', 'rating' => $avg_rating]
        );

        return redirect()->route('reviews')->with('success', 'Review updated successfully!');
    }


    public function unpublishedReviews()
    {
        $reviews = Review::with(['user', 'business.translations', 'translations'])
            ->whereIn('status', [Review::STATUS_PENDING_REVIEW, Review::STATUS_PROCESSING, Review::STATUS_REJECTED, Review::STATUS_REMOVED, 'inactive'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('Admin.reviews.index', compact('reviews'));
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        if ($review->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'You are not authorized to delete this review.');
        }
        ReviewTranslation::where('reviews_id', $review->id)->delete();
        $review->delete();

        return redirect()->back()->with('success', 'Your review has been deleted.');
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
        $request->validate([
            'rating'      => 'required|integer|min:1|max:5',
            'description' => 'required|string|max:5000',
            'title'       => 'required|string|max:500',
            'business_id' => 'required|exists:businesses,id',
            'status'      => 'nullable',
        ]);

        $review = Review::create([
            'rating'           => $request->rating,
            'business_id'      => $request->business_id,
            'user_id'          => auth()->id(),
            'lang_id'          => $lang_id,
            'status'           => Review::STATUS_PROCESSING,
            'submitted_locale' => ReviewModerationService::normalizeLocale(getCurrentLocale()),
        ]);

        $review->translations()->create([
            'reviews_id'  => $review->id,
            'title'       => $request->title,
            'description' => $request->description,
            'language_id' => $lang_id,
            'business_id' => $request->business_id,
        ]);

        try {
            \App\Jobs\ProcessReviewModeration::dispatch($review->id);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Admin review add dispatch error: ' . $e->getMessage());
        }

        return redirect()->route('reviews')->with('success', 'Review submitted and sent to automated moderation pipeline.');
    }

    public function reviewDelete($id)
    {
        $review = Review::find($id);
        if (!$review) {
            return redirect()->back()->with(['error' => 'Review not found']);
        }
        // Never physically delete moderation history: mark as removed
        $review->update(['status' => Review::STATUS_REMOVED]);

        \App\Services\ReviewTranslationService::handleRemovedOrRejectedReview($review);

        ReviewModerationLog::logModerator(
            $review->id,
            ReviewModerationLog::ACTION_MODERATED,
            auth()->id(),
            $review->status,
            Review::STATUS_REMOVED,
            ['action' => 'marked_removed']
        );

        return redirect()->back()->with(['success' => 'Review removed successfully']);
    }

    // --- 8. BATCH JOBS MANAGEMENT (Layer 3 GPT-5.6-Luna Batches) ----------------
    public function batches(Request $request)
    {
        $jobs = AiBatchJob::where('prompt_type', 'review_moderation')
            ->orderBy('id', 'desc')
            ->paginate(20);

        $waitingForBatchCount   = Review::readyForBatch()->count();
        $processingBatchesCount = AiBatchJob::where('prompt_type', 'review_moderation')->where('status', 'processing')->count();
        $completedBatchesCount  = AiBatchJob::where('prompt_type', 'review_moderation')->where('status', 'completed')->count();
        $failedBatchesCount     = AiBatchJob::where('prompt_type', 'review_moderation')->where('status', 'failed')->count();
        $lastPollAt             = Cache::get('review_batch_last_poll');

        return view('Admin.reviews.batches', compact(
            'jobs',
            'waitingForBatchCount',
            'processingBatchesCount',
            'completedBatchesCount',
            'failedBatchesCount',
            'lastPollAt'
        ));
    }

    public function dispatchBatchNow(Request $request)
    {
        $waitingCount = Review::readyForBatch()->count();

        if ($waitingCount === 0) {
            return back()->with('error', 'No reviews are currently waiting in PROCESSING queue for Layer 3 moderation.');
        }

        try {
            Artisan::call('moderation:dispatch-batch');
            $output = trim(Artisan::output());
            return back()->with('success', 'Batch dispatch triggered successfully: ' . ($output ?: "Dispatched {$waitingCount} reviews."));
        } catch (\Throwable $e) {
            Log::error('dispatchBatchNow error: ' . $e->getMessage());
            return back()->with('error', 'Failed to dispatch batch: ' . $e->getMessage());
        }
    }

    public function pollBatchNow(Request $request)
    {
        try {
            Artisan::call('moderation:poll-batch');
            $output = trim(Artisan::output());
            return back()->with('success', 'Batch status polled successfully. ' . $output);
        } catch (\Throwable $e) {
            Log::error('pollBatchNow error: ' . $e->getMessage());
            return back()->with('error', 'Failed to poll batch status: ' . $e->getMessage());
        }
    }

    public function downloadBatchJson(AiBatchJob $job)
    {
        $payload = null;
        if (!empty($job->input_payload)) {
            $payload = json_decode($job->input_payload, true);
        }

        if (!$payload) {
            $reviews = Review::where('localio_ai_batch_id', $job->batch_id)
                ->with(['translations', 'business.translations', 'user'])
                ->get();

            $payload = [
                'batch_id'    => $job->batch_id,
                'prompt_type' => 'review_moderation',
                'count'       => $reviews->count(),
                'reviews'     => $reviews->map(function ($r) {
                    $t = $r->translations->first();
                    return [
                        'review_id'     => $r->id,
                        'business_name' => $r->business?->translations?->first()?->name ?? 'Unknown',
                        'rating'        => $r->rating,
                        'title'         => $t?->title ?? '',
                        'text'          => $t?->description ?? '',
                        'submitted_locale' => $r->submitted_locale,
                    ];
                })->toArray(),
            ];
        }

        $content = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $filename = 'review_batch_' . $job->id . '_' . ($job->batch_id ?: 'job') . '.json';

        return response($content, 200, [
            'Content-Type'        => 'application/json; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function batchItems(AiBatchJob $job)
    {
        $reviews = Review::where('localio_ai_batch_id', $job->batch_id)
            ->with(['translations', 'business.translations', 'user'])
            ->paginate(50);

        return view('Admin.reviews.batch_items', compact('job', 'reviews'));
    }

    public function uploadBatchResult(Request $request, ?AiBatchJob $job = null)
    {
        $request->validate([
            'result_files'   => 'nullable|array',
            'result_files.*' => 'file|max:20480',
            'result_file'    => 'nullable|file|max:20480',
            'result_text'    => 'nullable|string',
        ]);

        $payloads = [];

        // 1. Multiple files
        if ($request->hasFile('result_files')) {
            $files = $request->file('result_files');
            if (!is_array($files)) {
                $files = [$files];
            }
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $payloads[] = [
                        'name' => $file->getClientOriginalName(),
                        'json' => file_get_contents($file->getRealPath()),
                    ];
                }
            }
        }

        // 2. Single file
        if (empty($payloads) && $request->hasFile('result_file')) {
            $file = $request->file('result_file');
            if ($file && $file->isValid()) {
                $payloads[] = [
                    'name' => $file->getClientOriginalName(),
                    'json' => file_get_contents($file->getRealPath()),
                ];
            }
        }

        // 3. Pasted text
        if (empty($payloads) && $request->filled('result_text')) {
            $payloads[] = [
                'name' => 'Pasted Text',
                'json' => trim($request->result_text),
            ];
        }

        if (empty($payloads)) {
            $err = 'Please upload at least one JSON/JSONL file or paste the result JSON text.';
            return ($request->expectsJson() || $request->ajax())
                ? response()->json(['success' => false, 'message' => $err], 422)
                : back()->with('error', $err);
        }

        $processedFiles = 0;
        $failedFiles    = 0;
        $totalProcessedReviews = 0;
        $totalFailedReviews    = 0;
        $jobsAffected   = [];
        $errors         = [];

        foreach ($payloads as $p) {
            $res = $this->processSingleReviewBatchResult($p['json'], $job);
            if ($res['success']) {
                $processedFiles++;
                if (!empty($res['job_id'])) {
                    $jobsAffected[$res['job_id']] = $res['batch_id'] ?? ('#' . $res['job_id']);
                }
                $totalProcessedReviews += $res['processed_items'] ?? 0;
                $totalFailedReviews    += $res['failed_items'] ?? 0;
            } else {
                $failedFiles++;
                $errors[] = $p['name'] . ': ' . ($res['error'] ?? 'Unknown error');
            }
        }

        $jobsCount = count($jobsAffected);
        $summaryMsg = "Batch results processed successfully: {$processedFiles} file(s) processed across {$jobsCount} batch job(s) ({$totalProcessedReviews} reviews updated, {$totalFailedReviews} failed).";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'           => $processedFiles > 0,
                'processed_files'   => $processedFiles,
                'failed_files'      => $failedFiles,
                'jobs_count'        => $jobsCount,
                'processed_reviews' => $totalProcessedReviews,
                'failed_reviews'    => $totalFailedReviews,
                'errors'            => $errors,
                'message'           => $summaryMsg,
            ], $processedFiles > 0 ? 200 : 422);
        }

        if ($failedFiles > 0 && $processedFiles === 0) {
            return back()->with('error', 'All files failed to process: ' . implode('; ', $errors));
        }

        if ($failedFiles > 0) {
            return back()->with('warning', $summaryMsg . ' Note: ' . $failedFiles . ' file(s) failed: ' . implode('; ', $errors));
        }

        return back()->with('success', $summaryMsg);
    }

    /**
     * Process a single review moderation batch JSON or JSONL payload string.
     */
    public function processSingleReviewBatchResult(string $rawJson, ?AiBatchJob $job = null): array
    {
        // Clean markdown code blocks
        $cleanJson = preg_replace('/^```(?:json)?\s*/i', '', $rawJson);
        $cleanJson = preg_replace('/\s*```$/', '', $cleanJson);
        $cleanJson = trim($cleanJson);

        if (empty($cleanJson)) {
            return ['success' => false, 'error' => 'Empty content'];
        }

        // Check if JSON Lines or JSON array
        $lines = explode("\n", $cleanJson);

        // If JSON array or single object
        if (str_starts_with($cleanJson, '[') || (str_starts_with($cleanJson, '{') && !str_contains($cleanJson, '"custom_id"'))) {
            $parsed = json_decode($cleanJson, true);
            if (!is_array($parsed)) {
                return ['success' => false, 'error' => 'Invalid JSON syntax: ' . json_last_error_msg()];
            }

            // Convert items array to JSONL-like format
            $items = isset($parsed['reviews']) ? $parsed['reviews'] : (isset($parsed['items']) ? $parsed['items'] : $parsed);
            $lines = [];
            foreach ($items as $item) {
                if (isset($item['review_id'])) {
                    $item['custom_id'] = 'review-' . $item['review_id'];
                }
                $lines[] = json_encode($item);
            }
        }

        $model     = config('openai.review_ai_model', 'gpt-5.6-luna');
        $promptId  = config('openai.review_prompt_id', '');
        $promptVer = config('openai.review_prompt_version', '1');

        if (!$job || !$job->exists) {
            // Find batch from first item if possible
            $firstParsed = json_decode($lines[0] ?? '{}', true);
            $batchId = $firstParsed['batch_id'] ?? null;
            if ($batchId) {
                $job = AiBatchJob::where('batch_id', $batchId)->first();
            }
        }

        if (!$job) {
            $job = AiBatchJob::create([
                'provider'     => 'openai',
                'batch_id'     => 'batch_manual_' . strtoupper(bin2hex(random_bytes(6))),
                'prompt_type'  => 'review_moderation',
                'status'       => 'completed',
                'submitted_at' => now(),
                'completed_at' => now(),
            ]);
        }

        $result = PollReviewModerationBatch::processBatchResultLines($job, $lines, $model, $promptId, $promptVer);

        $job->update([
            'status'          => 'completed',
            'completed_at'    => now(),
            'completed_items' => $result['processed'],
            'failed_items'    => $result['failed'],
        ]);

        return [
            'success'         => true,
            'job_id'          => $job->id,
            'batch_id'        => $job->batch_id,
            'processed_items' => $result['processed'],
            'failed_items'    => $result['failed'],
        ];
    }

    public function deleteBatchJob(AiBatchJob $job)
    {
        // If reviews are still processing for this batch, reset them to waiting
        Review::where('localio_ai_batch_id', $job->batch_id)
            ->where('localio_ai_status', Review::AI_STATUS_PROCESSING)
            ->update([
                'localio_ai_status' => Review::AI_STATUS_WAITING,
                'localio_ai_batch_id' => null,
            ]);

        $job->delete();

        return back()->with('success', 'Batch job removed successfully.');
    }
}
