<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Business;
use App\Models\BusinessTranslation;
use App\Models\AiContentQueue;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $completedBizIds = AiContentQueue::where('resource_type', 'business')
            ->where('status', 'completed')
            ->pluck('resource_id')
            ->unique()
            ->toArray();

        $populatedBizIds = Business::where(function ($q) use ($completedBizIds) {
            $q->whereHas('translations', function ($t) {
                $t->where(function ($sub) {
                    $sub->whereNotNull('short_description')->where('short_description', '!=', '')
                        ->orWhereNotNull('description')->where('description', '!=', '')
                        ->orWhereNotNull('long_preview_description')->where('long_preview_description', '!=', '');
                });
            })
            ->orWhereHas('usps')
            ->orWhereHas('features')
            ->orWhereHas('reviews')
            ->orWhereIn('id', $completedBizIds);
        })->pluck('id')->toArray();

        // Set all unpopulated/empty businesses imported into content generation to status = 0 (draft)
        Business::whereNotIn('id', $populatedBizIds)->update(['status' => 0]);
        BusinessTranslation::whereNotIn('business_id', $populatedBizIds)->update(['status' => 0]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert: set businesses to status 1
        Business::where('status', 0)->update(['status' => 1]);
        BusinessTranslation::where('status', 0)->update(['status' => 1]);
    }
};

