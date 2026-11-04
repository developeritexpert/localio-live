<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessClaim;
use App\Models\BusinessMember;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BusinessClaimController extends Controller
{
    /**
     * Display the Claim Profile search & wizard page.
     */
    public function index(Request $request, $slug = null)
    {
        $selectedBusiness = null;
        if ($slug) {
            $selectedBusiness = Business::whereHas('translations', function ($q) use ($slug) {
                $q->where('slug', $slug);
            })->with(['translations', 'category', 'category.translations'])->first();
        }

        return view('User.claim.index', [
            'selectedBusiness' => $selectedBusiness,
        ]);
    }

    /**
     * Live search for businesses by name or slug.
     */
    public function search(Request $request)
    {
        $query = trim($request->input('q', ''));
        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $langId = function_exists('getCurrentLanguageID') ? getCurrentLanguageID() : 1;

        $businesses = Business::where('status', 1)
            ->where(function ($q) use ($query, $langId) {
                $q->whereHas('translations', function ($t) use ($query) {
                    $t->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('slug', 'LIKE', "%{$query}%");
                });
            })
            ->with(['translations' => function ($t) use ($langId) {
                $t->where('lang_id', $langId);
            }, 'category', 'category.translations' => function ($ct) use ($langId) {
                $ct->where('lang_id', $langId);
            }])
            ->limit(10)
            ->get();

        $results = $businesses->map(function ($b) {
            $trans = $b->translations->first() ?: $b->translations()->first();
            $catTrans = $b->category?->translations?->first() ?: $b->category?->translations()->first();
            
            // Icon
            $iconUrl = null;
            if ($b->icon_id) {
                $media = \App\Models\Media::find($b->icon_id);
                if ($media) {
                    $iconUrl = asset($media->dir_path . '/' . $media->file_name);
                }
            }

            return [
                'id'             => $b->id,
                'name'           => $trans?->name ?? 'Business #' . $b->id,
                'slug'           => $trans?->slug ?? '',
                'category_name'  => $catTrans?->name ?? 'Category',
                'icon_url'       => $iconUrl,
                'website_domain' => $b->website_domain,
                'is_claimed'     => (bool)$b->is_claimed,
            ];
        });

        return response()->json(['results' => $results]);
    }

    /**
     * Start a claim (handles step submission for both automatic and manual verification).
     */
    public function startClaim(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success'       => false,
                'requires_auth' => true,
                'message'       => 'Please log in to your Localio account to claim this business profile.',
            ], 401);
        }

        $request->validate([
            'business_id'         => 'required|exists:businesses,id',
            'connection_type'     => 'required|in:owner,employee,representative,agency,other',
            'job_title'           => 'required|string|max:100',
            'verification_method' => 'required|in:automatic,manual',
            'agency_authorized'   => 'nullable|boolean',
        ]);

        $business = Business::with('translations')->findOrFail($request->business_id);
        $user = Auth::user();

        // Agency authorization check
        if ($request->connection_type === 'agency' && !$request->boolean('agency_authorized')) {
            return response()->json([
                'success' => false,
                'message' => 'As an agency, you must confirm that you are authorized to act for this business.',
            ], 422);
        }

        // Check if user is already a member
        if ($user->managesBusiness($business->id)) {
            return response()->json([
                'success'      => true,
                'already_member' => true,
                'redirect_url' => route('user.business.reviews', ['business_id' => $business->id]),
                'message'      => 'You are already a member managing this business profile.',
            ]);
        }

        $bizName = $business->translations->first()?->name ?? 'Business';

        // ── AUTOMATIC VERIFICATION ───────────────────────────────────────────
        if ($request->verification_method === 'automatic') {
            $request->validate([
                'work_email' => 'required|email|max:150',
            ]);

            $workEmail = strtolower(trim($request->work_email));

            // Domain match verification
            if (!$business->matchesEmailDomain($workEmail)) {
                $expectedDomain = $business->website_domain ?: 'the official business domain';
                return response()->json([
                    'success' => false,
                    'message' => "The email domain does not match {$expectedDomain}. Please use your official business email (e.g. name@{$expectedDomain}), or request manual verification below.",
                ], 422);
            }

            // Generate 6-digit code and token
            $code = (string)random_int(100000, 999999);
            $token = Str::random(40);
            $expiresAt = now()->addHours(24);

            $claim = BusinessClaim::create([
                'business_id'         => $business->id,
                'user_id'             => $user->id,
                'connection_type'     => $request->connection_type,
                'agency_authorized'   => $request->boolean('agency_authorized'),
                'job_title'           => $request->job_title,
                'verification_method' => 'automatic',
                'work_email'          => $workEmail,
                'verification_code'   => $code,
                'verification_token'  => $token,
                'code_expires_at'     => $expiresAt,
                'status'              => BusinessClaim::STATUS_PENDING_CODE,
            ]);

            $verificationUrl = route('claim.verify_link', ['locale' => app()->getLocale(), 'token' => $token]);

            // Dispatch verification email
            MailService::sendTemplate('business_claim_code', $workEmail, [
                'user_name'         => $user->first_name ?? 'User',
                'business_name'     => $bizName,
                'verification_code' => $code,
                'verification_url'  => $verificationUrl,
            ]);

            return response()->json([
                'success'    => true,
                'method'     => 'automatic',
                'claim_id'   => $claim->id,
                'work_email' => $workEmail,
                'message'    => "Verification code sent to {$workEmail}. Please enter the 6-digit code to complete verification.",
            ]);
        }

        // ── MANUAL VERIFICATION ──────────────────────────────────────────────
        $request->validate([
            'evidence_text'     => 'nullable|string|max:2000',
            'evidence_channel'  => 'nullable|string|max:255',
            'evidence_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $docPath = null;
        if ($request->hasFile('evidence_document')) {
            $file = $request->file('evidence_document');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $destDir = public_path('uploads/claim_documents');
            if (!file_exists($destDir)) {
                @mkdir($destDir, 0755, true);
            }
            $file->move($destDir, $filename);
            $docPath = 'uploads/claim_documents/' . $filename;
        }

        $claim = BusinessClaim::create([
            'business_id'         => $business->id,
            'user_id'             => $user->id,
            'connection_type'     => $request->connection_type,
            'agency_authorized'   => $request->boolean('agency_authorized'),
            'job_title'           => $request->job_title,
            'verification_method' => 'manual',
            'work_email'          => $request->input('work_email') ? strtolower(trim($request->input('work_email'))) : null,
            'evidence_text'       => $request->evidence_text,
            'evidence_channel'    => $request->evidence_channel,
            'evidence_document'   => $docPath,
            'status'              => BusinessClaim::STATUS_PENDING_MANUAL,
        ]);

        return response()->json([
            'success'  => true,
            'method'   => 'manual',
            'claim_id' => $claim->id,
            'message'  => 'Your claim request and verification evidence have been submitted. Our team will review your request manually and notify you by email.',
        ]);
    }

    /**
     * Submit and verify the 6-digit verification code.
     */
    public function verifyCode(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please log in.'], 401);
        }

        $request->validate([
            'claim_id'          => 'required|exists:business_claims,id',
            'verification_code' => 'required|string|size:6',
        ]);

        $claim = BusinessClaim::where('id', $request->claim_id)
            ->where('user_id', Auth::id())
            ->where('status', BusinessClaim::STATUS_PENDING_CODE)
            ->first();

        if (!$claim) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired claim request. Please start again.',
            ], 422);
        }

        if ($claim->code_expires_at && now()->isAfter($claim->code_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'This verification code has expired. Please request a new code.',
            ], 422);
        }

        if (trim($claim->verification_code) !== trim($request->verification_code)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect verification code. Please check your email and try again.',
            ], 422);
        }

        // Code matches -> Approve claim & grant ownership
        $this->approveAndAssignMembership($claim);

        return response()->json([
            'success'      => true,
            'redirect_url' => route('user.business.reviews', ['business_id' => $claim->business_id]),
            'message'      => 'Business profile verified successfully! You can now manage your business.',
        ]);
    }

    /**
     * Verify via email one-click link.
     */
    public function verifyLink($token)
    {
        $claim = BusinessClaim::where('verification_token', $token)
            ->where('status', BusinessClaim::STATUS_PENDING_CODE)
            ->first();

        if (!$claim) {
            return redirect()->route('claim.index')->with('error', 'Invalid or expired claim link.');
        }

        if ($claim->code_expires_at && now()->isAfter($claim->code_expires_at)) {
            return redirect()->route('claim.index')->with('error', 'This verification link has expired. Please claim again.');
        }

        // If not logged in, log in the claimant user
        if (!Auth::check() || Auth::id() !== $claim->user_id) {
            Auth::loginUsingId($claim->user_id);
        }

        $this->approveAndAssignMembership($claim);

        return redirect()->route('user.business.reviews', ['business_id' => $claim->business_id])
            ->with('success', 'Business profile verified successfully! Welcome to your business management portal.');
    }

    /**
     * Resend verification code.
     */
    public function resendCode(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please log in.'], 401);
        }

        $request->validate([
            'claim_id' => 'required|exists:business_claims,id',
        ]);

        $claim = BusinessClaim::where('id', $request->claim_id)
            ->where('user_id', Auth::id())
            ->where('status', BusinessClaim::STATUS_PENDING_CODE)
            ->firstOrFail();

        $code = (string)random_int(100000, 999999);
        $claim->update([
            'verification_code' => $code,
            'code_expires_at'   => now()->addHours(24),
        ]);

        $biz = $claim->business;
        $bizName = $biz?->translations?->first()?->name ?? 'Business';

        MailService::sendTemplate('business_claim_code', $claim->work_email, [
            'user_name'         => Auth::user()->first_name ?? 'User',
            'business_name'     => $bizName,
            'verification_code' => $code,
            'verification_url'  => route('claim.verify_link', ['token' => $claim->verification_token]),
        ]);

        return response()->json([
            'success' => true,
            'message' => "A new verification code has been sent to {$claim->work_email}.",
        ]);
    }

    /**
     * Internal helper to approve claim and grant membership.
     */
    protected function approveAndAssignMembership(BusinessClaim $claim)
    {
        DB::transaction(function () use ($claim) {
            $claim->update([
                'status'      => BusinessClaim::STATUS_APPROVED,
                'reviewed_at' => now(),
            ]);

            $business = Business::find($claim->business_id);
            if ($business) {
                $business->update(['is_claimed' => 1]);
            }

            // Create or update member as Owner
            BusinessMember::updateOrCreate(
                [
                    'business_id' => $claim->business_id,
                    'user_id'     => $claim->user_id,
                ],
                [
                    'role'        => BusinessMember::ROLE_OWNER,
                    'job_title'   => $claim->job_title,
                    'work_email'  => $claim->work_email,
                    'permissions' => BusinessMember::OWNER_PERMISSIONS,
                    'status'      => BusinessMember::STATUS_ACTIVE,
                ]
            );
        });

        // Send approval confirmation email
        $biz = Business::find($claim->business_id);
        $bizName = $biz?->translations?->first()?->name ?? 'Business';
        $user = User::find($claim->user_id);

        if ($user) {
            MailService::sendTemplate('business_claim_approved', $user->email, [
                'user_name'     => $user->first_name ?? 'User',
                'business_name' => $bizName,
                'dashboard_url' => route('user.business.reviews', ['business_id' => $claim->business_id]),
            ], null, $user);
        }
    }
}

