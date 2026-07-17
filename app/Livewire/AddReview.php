<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class AddReview extends Component
{
    public $businessId;
    public $rating = 0;
    public $title2 = '';
    public $comment = '';
    public $show = false;

    public $criteria = [];
    public $criteriaRatings = [];
    public $recommend = 1;
    public $pros;
    public $cons;

    #[On('openReviewModal')]
    public function openReviewModal($businessId)
    {
        $this->reset(['rating', 'comment', 'title2', 'pros', 'cons', 'criteriaRatings', 'recommend']);
        $this->businessId = $businessId;
        $this->recommend = 1;

        $business = \App\Models\Business::find($businessId);
        if ($business && $business->category) {
            $this->criteria = $business->category->ratingCriteria->toArray();
            foreach ($this->criteria as $criterion) {
                $this->criteriaRatings[$criterion['id']] = 0;
            }
        } else {
            $this->criteria = [];
        }

        $this->show = true;
    }

    public function submit()
    {
        $lang_id = getCurrentLanguageID();
    
        $rules = [
            'title2'    => 'required|string|max:500',
            'comment'   => 'required|string|max:1000',
            'pros'      => 'nullable|string',
            'cons'      => 'nullable|string',
            'recommend' => 'required|boolean',
        ];

        foreach ($this->criteria as $criterion) {
            $rules['criteriaRatings.' . $criterion['id']] = 'required|integer|min:1|max:5';
        }

        $this->validate($rules);
    
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
            'pros'        => $this->pros,
            'cons'        => $this->cons,
        ]);
    
        $this->dispatch('alert', ['type' => 'success', 'message' => 'Review submitted successfully.']);
    
        $this->show = false;
        $this->dispatch('review-submitted');
    }
    

    public function render()
    {
        return view('livewire.add-review');
    }
}