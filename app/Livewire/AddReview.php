<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Review;
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
    public $pros = '';
    public $cons = '';

    #[On('openReviewModal')]
    public function openReviewModal($businessId, $recommend = null)
    {
        $this->reset(['rating', 'comment', 'title2', 'pros', 'cons', 'criteriaRatings', 'recommend', 'step', 'reviewId']);
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
            if ($business->category) {
                $this->criteria = $business->category->ratingCriteria->toArray();
                foreach ($this->criteria as $criterion) {
                    $this->criteriaRatings[$criterion['id']] = 0;
                }
            } else {
                $this->criteria = [];
            }
        } else {
            $this->criteria = [];
            $this->businessName = 'Business';
        }

        $this->show = true;
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

    public function goToStep3()
    {
        $this->validate([
            'title2'  => 'required|string|max:500',
            'comment' => 'required|string|max:1000',
        ]);
        $this->step = 3;
    }

    public function setStep($stepNum)
    {
        $this->step = $stepNum;
    }

    public function submit()
    {
        $this->validate([
            'pros' => 'nullable|string',
            'cons' => 'nullable|string',
        ]);

        $this->createReviewRecord($this->pros, $this->cons);

        $this->dispatch('alert', ['type' => 'success', 'message' => 'Review submitted successfully.']);
        $this->show = false;
        $this->dispatch('review-submitted');
    }

    public function skipOptional()
    {
        $this->createReviewRecord('', '');

        $this->dispatch('alert', ['type' => 'success', 'message' => 'Review submitted successfully.']);
        $this->show = false;
        $this->dispatch('review-submitted');
    }

    protected function createReviewRecord($pros, $cons)
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
            $this->dispatch('alert', ['type' => 'error', 'message' => 'You have already submitted a review for this business.']);
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
        ]);

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
            'pros'        => $pros ?? '',
            'cons'        => $cons ?? '',
        ]);
    }

    public function render()
    {
        return view('livewire.add-review');
    }
}