<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Business;
use App\Models\Review;
use App\Models\CategoryProCon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class AddReview extends Component
{
    public $show = false;
    public $step = 1;
    public $businessId;
    public $businessName;
    public $businessIcon;
    public $criteria = [];
    public $criteriaRatings = [];
    public $recommend = null;
    public $title2;
    public $comment;
    public $availablePros = [];
    public $availableCons = [];
    public $selectedPros = [];
    public $selectedCons = [];
    public $proSearch = '';
    public $conSearch = '';
    public $reviewId;

    #[On('openReviewModal')]
    #[On('openAddReviewModal')]
    public function openReviewModal($businessId = null, $recommend = null)
    {
        if (is_array($businessId)) {
            $recommend = $businessId['recommend'] ?? $recommend;
            $businessId = $businessId['businessId'] ?? ($businessId['id'] ?? null);
        }

        if (!Auth::check()) {
            session([
                'pending_review_business_id' => $businessId,
                'pending_review_recommend'   => $recommend
            ]);
            return redirect()->route('login');
        }

        $this->reset([
            'step', 'businessId', 'businessName', 'businessIcon', 'criteria',
            'criteriaRatings', 'recommend', 'title2', 'comment',
            'availablePros', 'availableCons', 'selectedPros', 'selectedCons',
            'proSearch', 'conSearch', 'reviewId'
        ]);

        $this->step = 1;
        $this->businessId = $businessId;
        if ($recommend !== null) {
            $this->recommend = filter_var($recommend, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        }

        $business = Business::with(['translations', 'category', 'category.parent'])->find($businessId);

        if ($business) {
            $this->businessName = $business->translations->first()->name ?? 'Business';
            $this->businessIcon = $business->icon_id;

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

        // If existing review by user, load their selections & ratings
        $existingReview = Review::where('user_id', Auth::id())
            ->where('business_id', $this->businessId)
            ->first();
        if ($existingReview) {
            $this->reviewId = $existingReview->id;
            if ($this->recommend === null) {
                $this->recommend = $existingReview->recommend ? 1 : 0;
            }
            $trans = $existingReview->translations->first();
            if ($trans) {
                $this->title2 = $trans->title;
                $this->comment = $trans->description;
            }

            $existingRatings = \App\Models\ReviewRating::where('review_id', $existingReview->id)->get();
            foreach ($existingRatings as $rRating) {
                $this->criteriaRatings[$rRating->criteria_id] = $rRating->rating;
            }

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
        $this->proSearch = '';
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
        $this->conSearch = '';
    }

    public function clearProSearch()
    {
        $this->proSearch = '';
    }

    public function clearConSearch()
    {
        $this->conSearch = '';
    }

    public function goToStep2()
    {
        $this->validate([
            'recommend' => 'required|in:0,1',
        ], [
            'recommend.required' => 'Please select whether you recommend this business.',
            'recommend.in'       => 'Please select whether you recommend this business.',
        ]);

        $this->step = 2;
    }

    public function goToStep3()
    {
        $rules = [];
        $messages = [];
        foreach ($this->criteria as $criterion) {
            $rules['criteriaRatings.' . $criterion['id']] = 'required|integer|min:1|max:5';
            $messages['criteriaRatings.' . $criterion['id'] . '.min'] = 'Rating is required.';
            $messages['criteriaRatings.' . $criterion['id'] . '.required'] = 'Rating is required.';
        }

        $this->validate($rules, $messages);
        $this->step = 3;
    }

    public function submitStep3()
    {
        $this->validate([
            'title2'  => 'required|string|max:500',
            'comment' => 'required|string|max:1000',
        ]);

        // Save review record in DB immediately so it persists even if user aborts at step 4
        $this->createReviewRecord();

        $this->dispatch('alert', ['type' => 'success', 'message' => 'Review submitted successfully.']);
        // Advance to step 4 (Pros & Cons)
        $this->step = 4;
    }

    public function setStep($stepNum)
    {
        $this->step = (int) $stepNum;
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

        // Calculate average rating
        $totalRating = 0;
        $criteriaCount = count($this->criteriaRatings);
        foreach ($this->criteriaRatings as $ratingVal) {
            $totalRating += $ratingVal;
        }
        $avg_rating = $criteriaCount > 0 ? round($totalRating / $criteriaCount, 2) : 0;
        $lang_id = getCurrentLanguageID();

        $existingReview = Review::where('user_id', Auth::id())
            ->where('business_id', $this->businessId)
            ->first();

        if ($existingReview) {
            $this->reviewId = $existingReview->id;

            $existingReview->update([
                'rating'    => $avg_rating,
                'recommend' => (bool)$this->recommend,
            ]);

            if ($existingReview->translations->first()) {
                $existingReview->translations->first()->update([
                    'title'       => $this->title2,
                    'description' => $this->comment,
                ]);
            } else {
                $existingReview->translations()->create([
                    'business_id' => $this->businessId,
                    'language_id' => $lang_id,
                    'title'       => $this->title2,
                    'description' => $this->comment,
                ]);
            }

            // Save / update criteria ratings
            foreach ($this->criteriaRatings as $criteriaId => $ratingVal) {
                \App\Models\ReviewRating::updateOrCreate(
                    [
                        'review_id'   => $existingReview->id,
                        'criteria_id' => $criteriaId,
                    ],
                    [
                        'rating'      => $ratingVal
                    ]
                );
            }

            $selectedIds = array_merge($this->selectedPros, $this->selectedCons);
            $existingReview->selectedProCons()->sync($selectedIds);
            return;
        }

        $review = Review::create([
            'user_id'     => Auth::id(),
            'business_id' => $this->businessId,
            'lang_id'     => $lang_id,
            'rating'      => $avg_rating,
            'recommend'   => (bool)$this->recommend,
            'status'      => 'active',
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

            public function getFilteredProsProperty()
    {
        $unselected = array_values(array_filter($this->availablePros, function($pro) {
            return !in_array($pro['id'], $this->selectedPros);
        }));

        if (empty(trim($this->proSearch))) {
            return $unselected;
        }

        $search = strtolower(trim($this->proSearch));
        return array_values(array_filter($unselected, function($pro) use ($search) {
            return str_contains(strtolower($pro['text']), $search);
        }));
    }

    public function getFilteredConsProperty()
    {
        $unselected = array_values(array_filter($this->availableCons, function($con) {
            return !in_array($con['id'], $this->selectedCons);
        }));

        if (empty(trim($this->conSearch))) {
            return $unselected;
        }

        $search = strtolower(trim($this->conSearch));
        return array_values(array_filter($unselected, function($con) use ($search) {
            return str_contains(strtolower($con['text']), $search);
        }));
    }

    public function render()
    {
        return view('livewire.add-review', [
            'filteredPros' => $this->filteredPros,
            'filteredCons' => $this->filteredCons,
        ]);
    }
}

