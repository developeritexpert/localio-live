<?php

namespace App\Livewire\Vendor;

use Livewire\Component;
use App\Models\Business;
use App\Models\VendorReviewFeedback;

use Illuminate\Support\Facades\Auth;

class ReviewFilter extends Component
{

    public $businessId = 20;
    public $filterRating = '';
    public $filterDate = '';
    public $searchTerm = '';
    public $reviewId;
    public $showModal = false;
    public $message = '';
    public $isInappropriate = false;

    public function openModal($reviewId)
    {
        $this->reviewId = $reviewId;
        $this->resetValidation();
        $this->reset(['message', 'isInappropriate']);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function submit()
    {
        // dd("sdsd");
        $this->validate([
            'message' => 'required|string|max:1000',
        ]);

        VendorReviewFeedback::create([
            'user_id' => Auth::id(),
            'review_id' => $this->reviewId,
            'message' => $this->message,
            'is_inappropriate' => $this->isInappropriate,
        ]);

        // Dispatch toast
        $this->dispatch('swal:toast', [
            'type' => 'success',
            'message' => 'Your feedback has been sent to the admin.',
        ]);

        // Close modal via JS
        $this->dispatch('feedback-sent');

        $this->reset(['message', 'isInappropriate']);
        $this->closeModal(); // if you're using $showModal in Livewire
    }


    public function getFilteredReviewsProperty()
    {
        $business = Business::with(['reviews.translations'])->findOrFail($this->businessId);

        $reviews = $business->reviews();

        if ($this->filterRating) {
            // dd('here');
            $reviews->where('rating', '>=', $this->filterRating);
        }

        if ($this->filterDate) {
            $reviews->when($this->filterDate === 'today', fn($q) => $q->whereDate('created_at', today()));
            $reviews->when($this->filterDate === 'week', fn($q) => $q->whereBetween('created_at', [now()->startOfWeek(), now()]));
            $reviews->when($this->filterDate === 'month', fn($q) => $q->whereMonth('created_at', now()->month));
            $reviews->when($this->filterDate === 'year', fn($q) => $q->whereYear('created_at', now()->year));
        }

        if ($this->searchTerm) {
            $reviews->where(function ($q) {
                $q->whereHas('translations', function ($query) {
                    $query->where('title', 'like', '%' . $this->searchTerm . '%')
                          ->orWhere('description', 'like', '%' . $this->searchTerm . '%');
                })->orWhereHas('user', function ($query) {
                    $query->where('first_name', 'like', '%' . $this->searchTerm . '%');
                });
            });
        }


        return $reviews->latest()->get();
    }


    public function render()
    {
        return view('livewire.vendor.review-filter');
    }
}
