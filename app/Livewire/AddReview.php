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

    public $ease_of_use_rating = 0;
    public $value_for_money_rating = 0;
    public $customer_service_rating = 0;
    public $exclusive_service_rating = 0;
    public $pros;
    public $cons;



    #[On('openReviewModal')]
    public function openReviewModal($businessId)
    {
        $this->reset(['rating', 'comment']);
            $this->businessId = $businessId;

        $this->show = true;
    }

    public function submit()
    {
        $lang_id = getCurrentLanguageID();
    
        $this->validate([
       
            'ease_of_use_rating'       => 'required|integer|min:1|max:5',
            'value_for_money_rating'   => 'required|integer|min:1|max:5',
            'customer_service_rating'  => 'required|integer|min:1|max:5',
            'exclusive_service_rating' => 'required|integer|min:1|max:5',
            'title2'                   => 'nullable|string|max:500',
            'comment'                  => 'nullable|string|max:1000',
            'pros'                     => 'required|string|min:5',
            'cons'                     => 'required|string|min:5',
           
        ]);

        
    
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
        $avg_rating = round((
           
            $this->ease_of_use_rating +
            $this->value_for_money_rating +
            $this->customer_service_rating +
            $this->exclusive_service_rating
        ) / 4, 2);
    
        $review = Review::create([
            'user_id'                 => Auth::id(),
            'business_id'            => $this->businessId,
            'lang_id'                => $lang_id,
         
            'ease_of_use_rating'     => $this->ease_of_use_rating,
            'value_for_money_rating' => $this->value_for_money_rating,
            'customer_service_rating'=> $this->customer_service_rating,
            'exclusive_service_rating'=> $this->exclusive_service_rating,

            'rating'             => $avg_rating,
        ]);
    
        $review->translations()->create([
            'business_id' => $this->businessId,
            'language_id' => $lang_id,
            'title'       => $this->title2,
            'description' => $this->comment,
            'pros' => $this->pros,
            'cons' => $this->cons,
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