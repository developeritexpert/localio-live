<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\BusinessClaim;
use App\Models\BusinessMember;
use App\Models\BusinessCorrectionRequest;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminBusinessClaimController extends Controller
{
    /**
     * Display all business claims (manual review requests and automatic claims).
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending_manual_review');

        $query = BusinessClaim::with(['business.translations', 'user', 'reviewer']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $claims = $query->orderBy('created_at', 'desc')->paginate(20);

        $counts = [
            'pending'  => BusinessClaim::where('status', BusinessClaim::STATUS_PENDING_MANUAL)->count(),
            'approved' => BusinessClaim::where('status', BusinessClaim::STATUS_APPROVED)->count(),
            'rejected' => BusinessClaim::where('status', BusinessClaim::STATUS_REJECTED)->count(),
            'all'      => BusinessClaim::count(),
        ];

        return view('Admin.claims.index', [
            'claims' => $claims,
            'counts' => $counts,
            'status' => $status,
        ]);
    }

    /**
     * Approve a claim request.
     */
    public function approve(Request $request, $id)
    {
        $claim = BusinessClaim::with(['business.translations', 'user'])->findOrFail($id);

        DB::transaction(function () use ($claim, $request) {
            $claim->update([
                'status'      => BusinessClaim::STATUS_APPROVED,
                'admin_notes' => $request->input('admin_notes', 'Approved by administrator.'),
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            $business = Business::find($claim->business_id);
            if ($business) {
                $business->update(['is_claimed' => 1]);
            }

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

        // Notify claimant
        $bizName = $claim->business?->translations?->first()?->name ?? 'Business';
        if ($claim->user) {
            MailService::sendTemplate('business_claim_approved', $claim->user->email, [
                'user_name'     => $claim->user->first_name ?? 'User',
                'business_name' => $bizName,
                'dashboard_url' => route('user.business.reviews', ['business_id' => $claim->business_id]),
            ], null, $claim->user);
        }

        return back()->with('success', "Claim for {$bizName} approved successfully. The user is now the primary owner.");
    }

    /**
     * Reject a claim request.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $claim = BusinessClaim::with(['business.translations', 'user'])->findOrFail($id);

        $claim->update([
            'status'      => BusinessClaim::STATUS_REJECTED,
            'admin_notes' => $request->reason,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $bizName = $claim->business?->translations?->first()?->name ?? 'Business';
        if ($claim->user) {
            MailService::sendTemplate('business_claim_rejected', $claim->user->email, [
                'user_name'     => $claim->user->first_name ?? 'User',
                'business_name' => $bizName,
                'reason'        => $request->reason,
            ], null, $claim->user);
        }

        return back()->with('success', "Claim for {$bizName} has been rejected.");
    }

    /**
     * Display all profile correction requests.
     */
    public function corrections(Request $request)
    {
        $status = $request->input('status', 'pending');

        $query = BusinessCorrectionRequest::with(['business.translations', 'user', 'reviewer']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $corrections = $query->orderBy('created_at', 'desc')->paginate(20);

        $counts = [
            'pending'  => BusinessCorrectionRequest::where('status', BusinessCorrectionRequest::STATUS_PENDING)->count(),
            'applied'  => BusinessCorrectionRequest::where('status', BusinessCorrectionRequest::STATUS_APPLIED)->count(),
            'declined' => BusinessCorrectionRequest::where('status', BusinessCorrectionRequest::STATUS_DECLINED)->count(),
            'all'      => BusinessCorrectionRequest::count(),
        ];

        return view('Admin.claims.corrections', [
            'corrections' => $corrections,
            'counts'      => $counts,
            'status'      => $status,
        ]);
    }

    /**
     * Update status of a correction request.
     */
    public function updateCorrectionStatus(Request $request, $id)
    {
        $request->validate([
            'status'      => 'required|in:applied,declined',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $correction = BusinessCorrectionRequest::findOrFail($id);
        $correction->update([
            'status'      => $request->status,
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
        ]);

        return back()->with('success', "Correction request marked as {$request->status}.");
    }
}

