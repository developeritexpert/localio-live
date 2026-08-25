<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContributorBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ContributorBatchController extends Controller
{
    /**
     * Display all contributor batches.
     */
    public function index()
    {
        $contributorBatches = ContributorBatch::orderBy('min_reviews')
            ->get();

        return view(
            'Admin.contributor-batches.index',
            compact('contributorBatches')
        );
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('Admin.contributor-batches.add');
    }

    /**
     * Store a new contributor batch.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'badge_name' => [
                'required',
                'string',
                'max:255',
            ],
            'min_reviews' => [
                'required',
                'integer',
                'min:1',
            ],
            'max_reviews' => [
                'nullable',
                'integer',
                'min:1',
            ],
            'icon' => [
                'required',
                'image',
                'mimes:png,jpg,jpeg,webp,svg',
                'max:2048',
            ],
            'status' => [
                'required',
                'in:active,inactive',
            ],
        ]);

        $this->validateReviewRange(
            (int) $validated['min_reviews'],
            isset($validated['max_reviews'])
                ? (int) $validated['max_reviews']
                : null
        );

        DB::beginTransaction();

        try {
            $iconPath = null;

            if ($request->hasFile('icon')) {
                $iconPath = $request->file('icon')
                    ->store('contributor-batches', 'public');
            }

            ContributorBatch::create([
                'badge_name' => $validated['badge_name'],
                'min_reviews' => $validated['min_reviews'],
                'max_reviews' => $validated['max_reviews'] ?? null,
                'icon' => $iconPath,
                'status' => $validated['status'],
            ]);

            DB::commit();

            return redirect()
                ->route('contributor-batches')
                ->with(
                    'success',
                    'Contributor batch created successfully.'
                );
        } catch (\Throwable $e) {
            DB::rollBack();

            if (!empty($iconPath)) {
                Storage::disk('public')->delete($iconPath);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Error creating contributor batch.'
                );
        }
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $contributorBatch = ContributorBatch::findOrFail($id);

        return view(
            'Admin.contributor-batches.edit',
            compact('contributorBatch')
        );
    }

    /**
     * Update contributor batch.
     */
   public function update(Request $request, $id)
{
    $contributorBatch = ContributorBatch::findOrFail($id);

    $validated = $request->validate([
        'badge_name' => [
            'required',
            'string',
            'max:255',
        ],
        'min_reviews' => [
            'required',
            'integer',
            'min:1',
        ],
        'max_reviews' => [
            'nullable',
            'integer',
            'min:1',
        ],
        'icon' => [
            'nullable',
            'file',
            'mimes:png,jpg,jpeg,webp,svg',
            'max:2048',
        ],
        'status' => [
            'required',
            'in:active,inactive',
        ],
    ]);

    $this->validateReviewRange(
        (int) $validated['min_reviews'],
        isset($validated['max_reviews'])
            ? (int) $validated['max_reviews']
            : null,
        $contributorBatch->id
    );

    DB::beginTransaction();

    $oldIconPath = $contributorBatch->icon;
    $newIconPath = null;

    try {

        $updateData = [
            'badge_name' => $validated['badge_name'],
            'min_reviews' => $validated['min_reviews'],
            'max_reviews' => $validated['max_reviews'] ?? null,
            'status' => $validated['status'],
        ];

        /*
         * Upload new icon only when user selected a new file.
         */
        if ($request->hasFile('icon')) {

            $newIconPath = $request->file('icon')
                ->store('contributor-batches', 'public');

            $updateData['icon'] = $newIconPath;
        }

        $contributorBatch->update($updateData);

        DB::commit();

        /*
         * Delete old icon only AFTER database update succeeds.
         */
        if (
            $newIconPath &&
            $oldIconPath &&
            $oldIconPath !== $newIconPath
        ) {
            Storage::disk('public')->delete($oldIconPath);
        }

        return redirect()
            ->route('contributor-batches')
            ->with(
                'success',
                'Contributor batch updated successfully.'
            );

    } catch (\Throwable $e) {

        DB::rollBack();

        /*
         * If DB update failed, remove newly uploaded file.
         */
        if ($newIconPath) {
            Storage::disk('public')->delete($newIconPath);
        }

        return redirect()
            ->back()
            ->withInput()
            ->with(
                'error',
                'Error updating contributor batch: ' . $e->getMessage()
            );
    }
}
    /**
     * Delete contributor batch.
     */
    public function destroy($id)
    {
        $contributorBatch = ContributorBatch::findOrFail($id);

        $iconPath = $contributorBatch->icon;

        $contributorBatch->delete();

        if ($iconPath) {
            Storage::disk('public')->delete($iconPath);
        }

        return redirect()
            ->route('contributor-batches')
            ->with(
                'success',
                'Contributor batch deleted successfully.'
            );
    }

    /**
     * Validate contributor review range.
     *
     * Prevents overlapping ranges.
     */
    private function validateReviewRange(
        int $minReviews,
        ?int $maxReviews,
        ?int $ignoreId = null
    ): void {
        if (
            $maxReviews !== null &&
            $maxReviews < $minReviews
        ) {
            throw ValidationException::withMessages([
                'max_reviews' =>
                    'Maximum reviews must be greater than or equal to minimum reviews.',
            ]);
        }

        $query = ContributorBatch::query();

        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId);
        }

        $overlappingBatch = $query
            ->where('min_reviews', '<=', $maxReviews ?? PHP_INT_MAX)
            ->where(function ($query) use ($minReviews) {
                $query
                    ->whereNull('max_reviews')
                    ->orWhere('max_reviews', '>=', $minReviews);
            })
            ->exists();

        if ($overlappingBatch) {
            throw ValidationException::withMessages([
                'min_reviews' =>
                    'This review range overlaps with an existing contributor batch.',
            ]);
        }
    }
}