<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessMember;
use App\Models\BusinessReviewReply;
use App\Models\BusinessReviewInvitation;
use App\Models\BusinessCorrectionRequest;
use App\Models\Review;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BusinessManagementController extends Controller
{
    /**
     * Authorize that the authenticated user is an active member of this business.
     */
    protected function authorizeMembership($businessId = null, ?string $requiredPermission = null): array
    {
        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        if (!is_numeric($businessId)) {
            $businessId = request()->route('business_id');
        }

        $membership = BusinessMember::where('business_id', $businessId)
            ->where('user_id', $user->id)
            ->where('status', BusinessMember::STATUS_ACTIVE)
            ->with(['business.translations'])
            ->first();

        if (!$membership) {
            abort(403, 'You do not have permission to manage this business profile.');
        }

        if ($requiredPermission && !$membership->hasPermission($requiredPermission)) {
            abort(403, 'You do not have permission to perform this action.');
        }

        return [$membership->business, $membership];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 1. REVIEWS
    // ─────────────────────────────────────────────────────────────────────────
    public function reviews(Request $request, ...$params)
    {
        $business_id = $request->route('business_id');
        [$business, $membership] = $this->authorizeMembership($business_id);

        $query = Review::where('business_id', $business->id)
            ->with(['user', 'translations', 'businessReply', 'ratings.criteria', 'selectedProCons']);

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', '>=', (float)$request->rating)
                  ->where('rating', '<', (float)$request->rating + 1);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default: show published & active
            $query->whereIn('status', [Review::STATUS_PUBLISHED, 'active']);
        }

        // Filter by reply status
        if ($request->filter_reply === 'replied') {
            $query->has('businessReply');
        } elseif ($request->filter_reply === 'unreplied') {
            $query->doesntHave('businessReply');
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('user_dashboard.business.reviews', [
            'business'   => $business,
            'membership' => $membership,
            'reviews'    => $reviews,
        ]);
    }

    public function storeReply(Request $request, ...$params)
    {
        $business_id = $request->route('business_id');
        $review_id = $request->route('review_id');
        [$business, $membership] = $this->authorizeMembership($business_id, 'can_reply_reviews');

        $request->validate([
            'reply_text' => 'required|string|max:3000',
        ]);

        $review = Review::where('id', $review_id)->where('business_id', $business->id)->firstOrFail();

        $reply = BusinessReviewReply::updateOrCreate(
            [
                'review_id'   => $review->id,
                'business_id' => $business->id,
            ],
            [
                'user_id'    => Auth::id(),
                'reply_text' => trim($request->reply_text),
                'status'     => BusinessReviewReply::STATUS_PUBLISHED,
            ]
        );

        return response()->json([
            'success'    => true,
            'message'    => 'Official response published successfully.',
            'reply_text' => $reply->reply_text,
            'date_human' => $reply->updated_at->diffForHumans(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. GET REVIEWS (Direct link & Email Invitations)
    // ─────────────────────────────────────────────────────────────────────────
    public function getReviews(Request $request, ...$params)
    {
        $business_id = $request->route('business_id');
        [$business, $membership] = $this->authorizeMembership($business_id);

        $locale = app()->getLocale() ?: 'en-us';
        $slug = $business->translations->first()?->slug ?? $business->id;
        $directReviewUrl = url("/{$locale}/{$slug}?action=review");

        $invitations = BusinessReviewInvitation::where('business_id', $business->id)
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('user_dashboard.business.get_reviews', [
            'business'        => $business,
            'membership'      => $membership,
            'directReviewUrl' => $directReviewUrl,
            'invitations'     => $invitations,
        ]);
    }

    public function sendInvitations(Request $request, ...$params)
    {
        $business_id = $request->route('business_id');
        [$business, $membership] = $this->authorizeMembership($business_id, 'can_invite_reviews');

        $request->validate([
            'emails' => 'required|string',
        ]);

        $rawEmails = preg_split('/[\r\n,;]+/', $request->emails);
        $validEmails = [];

        foreach ($rawEmails as $email) {
            $trimmed = strtolower(trim($email));
            if (filter_var($trimmed, FILTER_VALIDATE_EMAIL)) {
                $validEmails[] = $trimmed;
            }
        }

        $validEmails = array_values(array_unique($validEmails));

        if (empty($validEmails)) {
            return back()->with('error', 'No valid email addresses found. Please paste valid customer emails.');
        }

        if (count($validEmails) > 50) {
            return back()->with('error', 'You can send up to 50 review invitations at once.');
        }

        $locale = app()->getLocale() ?: 'en-us';
        $slug = $business->translations->first()?->slug ?? $business->id;
        $bizName = $business->translations->first()?->name ?? 'Business';

        $sentCount = 0;
        foreach ($validEmails as $email) {
            $token = Str::random(40);
            $reviewUrl = url("/{$locale}/{$slug}?action=review&invitation={$token}");

            BusinessReviewInvitation::create([
                'business_id'      => $business->id,
                'user_id'          => Auth::id(),
                'recipient_email'  => $email,
                'invitation_token' => $token,
                'status'           => BusinessReviewInvitation::STATUS_SENT,
            ]);

            MailService::sendTemplate('business_review_invitation', $email, [
                'business_name' => $bizName,
                'review_url'    => $reviewUrl,
            ]);

            $sentCount++;
        }

        return back()->with('success', "Successfully sent {$sentCount} review invitation(s)!");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. RATINGS & FEEDBACK (Analytics, Pros/Cons, Trends)
    // ─────────────────────────────────────────────────────────────────────────
    public function ratingsFeedback(Request $request, ...$params)
    {
        $business_id = $request->route('business_id');
        [$business, $membership] = $this->authorizeMembership($business_id);

        $reviews = Review::where('business_id', $business->id)
            ->whereIn('status', [Review::STATUS_PUBLISHED, 'active'])
            ->get();

        $totalReviews = $reviews->count();
        $avgRating = $totalReviews > 0 ? round($reviews->avg('rating'), 2) : 0;
        $recommendCount = $reviews->where('recommend', 1)->count();
        $recommendRate = $totalReviews > 0 ? round(($recommendCount / $totalReviews) * 100) : 0;

        // Rating distribution (1 to 5 stars)
        $ratingDistribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($reviews as $rev) {
            $star = (int)round($rev->rating);
            if ($star >= 1 && $star <= 5) {
                $ratingDistribution[$star]++;
            }
        }

        // Individual criteria breakdown
        $criteriaRatings = DB::table('review_ratings')
            ->join('reviews', 'review_ratings.review_id', '=', 'reviews.id')
            ->join('category_rating_criteria', 'review_ratings.criteria_id', '=', 'category_rating_criteria.id')
            ->where('reviews.business_id', $business->id)
            ->whereIn('reviews.status', [Review::STATUS_PUBLISHED, 'active'])
            ->select(
                'category_rating_criteria.name',
                'category_rating_criteria.default_key',
                DB::raw('ROUND(AVG(review_ratings.rating), 2) as avg_score'),
                DB::raw('COUNT(review_ratings.id) as total_ratings')
            )
            ->groupBy('category_rating_criteria.id', 'category_rating_criteria.name', 'category_rating_criteria.default_key')
            ->get();

        // Community Pros & Cons (frequency of selections)
        $topPros = DB::table('review_pro_cons')
            ->join('category_pro_cons', 'review_pro_cons.category_pro_con_id', '=', 'category_pro_cons.id')
            ->join('reviews', 'review_pro_cons.review_id', '=', 'reviews.id')
            ->where('reviews.business_id', $business->id)
            ->whereIn('reviews.status', [Review::STATUS_PUBLISHED, 'active'])
            ->where('category_pro_cons.type', 'pro')
            ->select('category_pro_cons.text', DB::raw('COUNT(review_pro_cons.id) as count'))
            ->groupBy('category_pro_cons.id', 'category_pro_cons.text')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        $topCons = DB::table('review_pro_cons')
            ->join('category_pro_cons', 'review_pro_cons.category_pro_con_id', '=', 'category_pro_cons.id')
            ->join('reviews', 'review_pro_cons.review_id', '=', 'reviews.id')
            ->where('reviews.business_id', $business->id)
            ->whereIn('reviews.status', [Review::STATUS_PUBLISHED, 'active'])
            ->where('category_pro_cons.type', 'con')
            ->select('category_pro_cons.text', DB::raw('COUNT(review_pro_cons.id) as count'))
            ->groupBy('category_pro_cons.id', 'category_pro_cons.text')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Monthly trends (past 6 months)
        $trends = DB::table('reviews')
            ->where('business_id', $business->id)
            ->whereIn('status', [Review::STATUS_PUBLISHED, 'active'])
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(id) as count'),
                DB::raw('ROUND(AVG(rating), 2) as avg_rating')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        return view('user_dashboard.business.ratings_feedback', [
            'business'           => $business,
            'membership'         => $membership,
            'totalReviews'       => $totalReviews,
            'avgRating'          => $avgRating,
            'recommendRate'      => $recommendRate,
            'ratingDistribution' => $ratingDistribution,
            'criteriaRatings'    => $criteriaRatings,
            'topPros'            => $topPros,
            'topCons'            => $topCons,
            'trends'             => $trends,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 4. TEAM MANAGEMENT
    // ─────────────────────────────────────────────────────────────────────────
    public function team(Request $request, ...$params)
    {
        $business_id = $request->route('business_id');
        [$business, $membership] = $this->authorizeMembership($business_id);

        $members = BusinessMember::where('business_id', $business->id)
            ->with(['user', 'inviter'])
            ->orderByRaw("CASE WHEN role = 'owner' THEN 1 WHEN role = 'admin' THEN 2 ELSE 3 END")
            ->get();

        return view('user_dashboard.business.team', [
            'business'   => $business,
            'membership' => $membership,
            'members'    => $members,
        ]);
    }

    public function inviteTeamMember(Request $request, ...$params)
    {
        $business_id = $request->route('business_id');
        [$business, $membership] = $this->authorizeMembership($business_id, 'can_manage_team');

        $request->validate([
            'email'     => 'required|email|max:150',
            'role'      => 'required|in:admin,member',
            'job_title' => 'nullable|string|max:100',
        ]);

        $email = strtolower(trim($request->email));
        $bizName = $business->translations->first()?->name ?? 'Business';

        // Check if already a member
        $existing = BusinessMember::where('business_id', $business->id)
            ->where(function ($q) use ($email) {
                $q->where('work_email', $email)
                  ->orWhereHas('user', fn($u) => $u->where('email', $email));
            })->first();

        if ($existing) {
            return back()->with('error', "{$email} is already a member of this team.");
        }

        // Check if user exists on Localio
        $targetUser = User::where('email', $email)->first();
        $token = Str::random(40);

        $permissions = [
            'can_reply_reviews'       => $request->boolean('perm_reply_reviews', true),
            'can_report_reviews'      => $request->boolean('perm_report_reviews', true),
            'can_invite_reviews'      => $request->boolean('perm_invite_reviews', true),
            'can_manage_team'         => $request->role === 'admin',
            'can_suggest_corrections' => $request->boolean('perm_suggest_corrections', true),
        ];

        if ($targetUser) {
            // Instantly attach active membership
            BusinessMember::create([
                'business_id'      => $business->id,
                'user_id'          => $targetUser->id,
                'role'             => $request->role,
                'job_title'        => $request->job_title,
                'work_email'       => $email,
                'permissions'      => $permissions,
                'invited_by'       => Auth::id(),
                'status'           => BusinessMember::STATUS_ACTIVE,
            ]);
        } else {
            // Invite pending registration
            // We create a placeholder user or save invitation token
            $placeholderUser = User::create([
                'first_name' => explode('@', $email)[0],
                'last_name'  => '',
                'email'      => $email,
                'password'   => bcrypt(Str::random(16)),
                'user_type'  => 'user',
                'status'     => 1,
            ]);

            BusinessMember::create([
                'business_id'      => $business->id,
                'user_id'          => $placeholderUser->id,
                'role'             => $request->role,
                'job_title'        => $request->job_title,
                'work_email'       => $email,
                'permissions'      => $permissions,
                'invited_by'       => Auth::id(),
                'status'           => BusinessMember::STATUS_INVITED,
                'invitation_token' => $token,
            ]);
        }

        $invitationUrl = route('user.business.reviews', ['business_id' => $business->id]);

        MailService::sendTemplate('business_team_invitation', $email, [
            'inviter_name'   => Auth::user()->name ?: 'A team member',
            'business_name'  => $bizName,
            'role'           => ucfirst($request->role),
            'invitation_url' => $invitationUrl,
        ]);

        return back()->with('success', "Team member invitation sent to {$email}!");
    }

    public function updateMember(Request $request, ...$params)
    {
        $business_id = $request->route('business_id');
        $member_id = $request->route('member_id');
        [$business, $membership] = $this->authorizeMembership($business_id, 'can_manage_team');

        $member = BusinessMember::where('id', $member_id)->where('business_id', $business->id)->firstOrFail();

        // Protect primary owner from demotion unless transferring
        if ($member->isOwner() && $member->user_id === Auth::id()) {
            return back()->with('error', 'You cannot change your own owner role. Use Transfer Ownership instead.');
        }

        $request->validate([
            'role'      => 'required|in:admin,member',
            'job_title' => 'nullable|string|max:100',
        ]);

        $member->update([
            'role'        => $request->role,
            'job_title'   => $request->job_title,
            'permissions' => [
                'can_reply_reviews'       => $request->boolean('perm_reply_reviews'),
                'can_report_reviews'      => $request->boolean('perm_report_reviews'),
                'can_invite_reviews'      => $request->boolean('perm_invite_reviews'),
                'can_manage_team'         => $request->role === 'admin' || $request->boolean('perm_manage_team'),
                'can_suggest_corrections' => $request->boolean('perm_suggest_corrections'),
            ],
        ]);

        return back()->with('success', 'Member permissions updated successfully.');
    }

    public function removeMember(Request $request, ...$params)
    {
        $business_id = $request->route('business_id');
        $member_id = $request->route('member_id');
        [$business, $membership] = $this->authorizeMembership($business_id, 'can_manage_team');

        $member = BusinessMember::where('id', $member_id)->where('business_id', $business->id)->firstOrFail();

        if ($member->isOwner()) {
            return back()->with('error', 'The owner cannot be removed. You can transfer ownership or leave if another administrator exists.');
        }

        $member->delete();

        return back()->with('success', 'Team member removed.');
    }

    public function leaveBusiness(Request $request, ...$params)
    {
        $business_id = $request->route('business_id');
        [$business, $membership] = $this->authorizeMembership($business_id);

        if ($membership->isOwner()) {
            // Check if there is another admin/owner to take over
            $successor = BusinessMember::where('business_id', $business->id)
                ->where('user_id', '!=', Auth::id())
                ->where('role', BusinessMember::ROLE_ADMIN)
                ->first();

            if (!$successor) {
                return back()->with('error', 'You are the only owner/admin. Please promote another team member to Admin before leaving this profile.');
            }

            // Promote successor to Owner
            $successor->update([
                'role'        => BusinessMember::ROLE_OWNER,
                'permissions' => BusinessMember::OWNER_PERMISSIONS,
            ]);
        }

        $membership->delete();

        // If no members remain, reset is_claimed
        $remaining = BusinessMember::where('business_id', $business->id)->count();
        if ($remaining === 0) {
            $business->update(['is_claimed' => 0]);
        }

        return redirect()->route('user-dashboard')->with('success', "You have left the management team for {$business->name}.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 5. PROFILE ISSUES (Private Suggestions to Localio Admins)
    // ─────────────────────────────────────────────────────────────────────────
    public function profileIssues(Request $request, ...$params)
    {
        $business_id = $request->route('business_id');
        [$business, $membership] = $this->authorizeMembership($business_id);

        $corrections = BusinessCorrectionRequest::where('business_id', $business->id)
            ->with(['user', 'reviewer'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('user_dashboard.business.profile_issues', [
            'business'    => $business,
            'membership'  => $membership,
            'corrections' => $corrections,
        ]);
    }

    public function storeCorrection(Request $request, ...$params)
    {
        $business_id = $request->route('business_id');
        [$business, $membership] = $this->authorizeMembership($business_id, 'can_suggest_corrections');

        $request->validate([
            'issue_type'           => 'required|in:general_info,pricing,features,contact,other',
            'suggested_correction' => 'required|string|max:3000',
            'current_info'         => 'nullable|string|max:1000',
            'supporting_link'      => 'nullable|url|max:255',
        ]);

        BusinessCorrectionRequest::create([
            'business_id'          => $business->id,
            'user_id'              => Auth::id(),
            'issue_type'           => $request->issue_type,
            'current_info'         => $request->current_info,
            'suggested_correction' => $request->suggested_correction,
            'supporting_link'      => $request->supporting_link,
            'status'               => BusinessCorrectionRequest::STATUS_PENDING,
        ]);

        return back()->with('success', 'Your correction request has been submitted to Localio administrators. We will review and verify the details.');
    }
}
