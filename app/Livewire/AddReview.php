<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Review;
use App\Models\CategoryProCon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class AddReview extends Component
{
    public $businessId;
    public $businessName;
    public $businessIcon;
    public $rating = 0;
    public $title2 = '';
    public $comment = '';
    public $show = false;
    public $step = 1;
    public $reviewId;

    public $criteria = [];
    public $criteriaRatings = [];
    public $recommend = 1;

    // Available and selected Category Pros & Cons
    public $availablePros = [];
    public $availableCons = [];
    public $selectedPros = [];
    public $selectedCons = [];

    #[On('openReviewModal')]
    public function openReviewModal($businessId, $recommend = null)
    {
        if (!Auth::check()) {
            session([
                'pending_review_business_id' => $businessId,
                'pending_review_recommend'   => $recommend,
                'register_from_modal'        => true,
            ]);
            $this->dispatch('show-login-modal');
            return;
        }

        $this->reset(['rating', 'comment', 'title2', 'selectedPros', 'selectedCons', 'criteriaRatings', 'recommend', 'step', 'reviewId']);
        $this->businessId = $businessId;
        
        // If recommend parameter is passed from thumbs up/down, set it. Otherwise default to 1 (Yes)
        if ($recommend !== null) {
            $this->recommend = $recommend ? 1 : 0;
        } else {
            $this->recommend = 1;
        }

        $business = \App\Models\Business::find($businessId);
        if ($business) {
            $this->businessName = $business->translations->first()->name ?? 'Business';
            $this->businessIcon = $business->icon_id;

            // Determine all relevant category IDs (direct category + parent category)
            $categoryIds = array_filter(array_unique([
                $business->category_id,
                $business->category ? $business->category->parent_id : null,
                $business->category ? $business->category->id : null,
            ]));

            if ($business->category) {
                $this->criteria = $business->category->ratingCriteria->toArray();
                if (empty($this->criteria) && $business->category->parent) {
                    $this->criteria = $business->category->parent->ratingCriteria->toArray();
                }
                foreach ($this->criteria as $criterion) {
                    $this->criteriaRatings[$criterion['id']] = 0;
                }
            } else {
                $this->criteria = [];
            }

            if (!empty($categoryIds)) {
                // Load category pre-defined pros & cons from direct or parent category
                $allProCons = CategoryProCon::whereIn('category_id', $categoryIds)
                    ->where('status', 1)
                    ->get();

                $this->availablePros = $allProCons->where('type', 'pro')->values()->toArray();
                $this->availableCons = $allProCons->where('type', 'con')->values()->toArray();
            } else {
                $this->availablePros = [];
                $this->availableCons = [];
            }
        } else {
            $this->criteria = [];
            $this->availablePros = [];
            $this->availableCons = [];
            $this->businessName = 'Business';
        }

        // If existing review by user, load their selections
        $existingReview = Review::where('user_id', Auth::id())
            ->where('business_id', $this->businessId)
            ->first();
        if ($existingReview) {
            $existingIds = $existingReview->selectedProCons()->pluck('category_pro_cons.id')->toArray();
            $this->selectedPros = CategoryProCon::whereIn('id', $existingIds)->where('type', 'pro')->pluck('id')->toArray();
            $this->selectedCons = CategoryProCon::whereIn('id', $existingIds)->where('type', 'con')->pluck('id')->toArray();
        }

        $this->show = true;
    }

    public function togglePro($id)
    {
        $id = (int) $id;
        if (in_array($id, $this->selectedPros)) {
            $this->selectedPros = array_values(array_diff($this->selectedPros, [$id]));
        } else {
            if (count($this->selectedPros) >= 5) {
                $this->dispatch('alert', ['type' => 'error', 'message' => 'You can select up to 5 Pros maximum.']);
                return;
            }
            $this->selectedPros[] = $id;
        }
    }

    public function toggleCon($id)
    {
        $id = (int) $id;
        if (in_array($id, $this->selectedCons)) {
            $this->selectedCons = array_values(array_diff($this->selectedCons, [$id]));
        } else {
            if (count($this->selectedCons) >= 5) {
                $this->dispatch('alert', ['type' => 'error', 'message' => 'You can select up to 5 Cons maximum.']);
                return;
            }
            $this->selectedCons[] = $id;
        }
    }

    public function goToStep2()
    {
        $rules = [];
        foreach ($this->criteria as $criterion) {
            $rules['criteriaRatings.' . $criterion['id']] = 'required|integer|min:1|max:5';
        }
        $rules['recommend'] = 'required|boolean';

        $this->validate($rules);
        $this->step = 2;
    }

    public function submitStep2()
    {
        $this->validate([
            'title2'  => 'required|string|max:500',
            'comment' => 'required|string|max:1000',
        ]);

        // Save review record in DB immediately so it persists even if user aborts at step 3
        $this->createReviewRecord();

        $this->dispatch('alert', ['type' => 'success', 'message' => 'Review submitted successfully.']);
        // Advance to step 3
        $this->step = 3;
    }

    public function setStep($stepNum)
    {
        $this->step = $stepNum;
    }

    public function submit()
    {
        if ($this->reviewId) {
            $reviewModel = Review::find($this->reviewId);
            if ($reviewModel) {
                $selectedIds = array_merge($this->selectedPros, $this->selectedCons);
                $reviewModel->selectedProCons()->sync($selectedIds);
            }
        } else {
            $this->createReviewRecord();
        }

        $this->dispatch('alert', ['type' => 'success', 'message' => 'Review updated successfully.']);
        $this->show = false;
        $this->dispatch('review-submitted');
    }

    public function closeModal()
    {
        $this->show = false;
        if ($this->reviewId) {
            $this->dispatch('review-submitted');
        }
    }

    protected function createReviewRecord()
    {
        if (!$this->businessId) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Something went wrong. Please refresh and try again.']);
            return;
        }

        $existingReview = Review::where('user_id', Auth::id())
            ->where('business_id', $this->businessId)
            ->first();

        if ($existingReview) {
            if ($existingReview->status === 'inactive') {
                $this->dispatch('alert', ['type' => 'error', 'message' => 'Your review has been disabled by the administrator.']);
                return;
            }
            $this->reviewId = $existingReview->id;
            if ($existingReview->translations->first()) {
                $existingReview->translations->first()->update([
                    'title'       => $this->title2,
                    'description' => $this->comment,
                ]);
            }
            $selectedIds = array_merge($this->selectedPros, $this->selectedCons);
            $existingReview->selectedProCons()->sync($selectedIds);
            return;
        }

        // Calculate average rating
        $totalRating = 0;
        $criteriaCount = count($this->criteriaRatings);
        foreach ($this->criteriaRatings as $ratingVal) {
            $totalRating += $ratingVal;
        }
        $avg_rating = $criteriaCount > 0 ? round($totalRating / $criteriaCount, 2) : 0;
        $lang_id = getCurrentLanguageID();

        $review = Review::create([
            'user_id'     => Auth::id(),
            'business_id' => $this->businessId,
            'lang_id'     => $lang_id,
            'rating'      => $avg_rating,
            'recommend'   => (bool)$this->recommend,
            'status'      => 'inactive',
        ]);

        $this->reviewId = $review->id;

        // Save individual criteria ratings
        foreach ($this->criteriaRatings as $criteriaId => $ratingVal) {
            \App\Models\ReviewRating::create([
                'review_id'   => $review->id,
                'criteria_id' => $criteriaId,
                'rating'      => $ratingVal
            ]);
        }

        $review->translations()->create([
            'business_id' => $this->businessId,
            'language_id' => $lang_id,
            'title'       => $this->title2,
            'description' => $this->comment,
        ]);

        // Sync selected pros and cons
        $selectedIds = array_merge($this->selectedPros, $this->selectedCons);
        $review->selectedProCons()->sync($selectedIds);
    }

    public function render()
    {
        return view('livewire.add-review');
    }
}