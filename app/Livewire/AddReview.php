<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Business;
use App\Models\Review;
use App\Services\MailService;
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
    public $categoryIds = [];
    public $selectedPros = [];
    public $selectedCons = [];
    public $proSearch = '';
    public $conSearch = '';
    public $proLimit = 30;
    public $conLimit = 30;
    public $reviewId;

    protected $rules = [];

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
                'pending_review_recommend'   => $recommend,
                'register_from_modal'        => true,
            ]);
            $this->dispatch('show-login-modal');
            return;
        }

        $this->reset([
            'step', 'businessId', 'businessName', 'businessIcon', 'criteria',
            'criteriaRatings', 'recommend', 'title2', 'comment',
            'categoryIds', 'selectedPros', 'selectedCons',
            'proSearch', 'conSearch', 'proLimit', 'conLimit', 'reviewId'
        ]);

        $this->step = 1;
        $this->businessId = $businessId;
        if ($recommend !== null) {
            $this->recommend = filter_var($recommend, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            $this->step = 2;
        }

        $business = Business::with(['translations', 'category', 'category.parent'])->find($businessId);

        if ($business) {
            $this->businessName = $business->translations->first()->name ?? 'Business';
            $this->businessIcon = $business->icon_id;

            $category = $business->category ?? \App\Models\Category::whereNull('parent_id')->orderBy('id')->first();

            $this->categoryIds = array_values(array_filter(array_unique([
                $business->category_id,
                $category ? $category->parent_id : null,
                $category ? $category->id : null,
            ])));

            if ($category) {
                $this->criteria = $category->getEffectiveRatingCriteria()->toArray();
                foreach ($this->criteria as $criterion) {
                    $this->criteriaRatings[$criterion['id']] = 0;
                }
            } else {
                $this->criteria = [];
            }
        } else {
            $this->categoryIds = [];
            $this->criteria = [];
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
        $this->proLimit = 30;
    }

    public function clearConSearch()
    {
        $this->conSearch = '';
        $this->conLimit = 30;
    }

    public function updatingProSearch()
    {
        $this->proLimit = 30;
    }

    public function updatingConSearch()
    {
        $this->conLimit = 30;
    }

    public function loadMorePros()
    {
        if (empty($this->proSearch)) {
            $this->proLimit += 30;
        }
    }

    public function loadMoreCons()
    {
        if (empty($this->conSearch)) {
            $this->conLimit += 30;
        }
    }

    public function selectRecommend($val)
    {
        $this->recommend = (int) $val;
        $this->step = 2;
    }

    public function goToStep2()
    {
        $this->validate([
            'recommend' => 'required|in:0,1',
        ], [
            'recommend.required' => static_text('error_review_recommend_required') ?: 'Please select whether you recommend this business.',
            'recommend.in'       => static_text('error_review_recommend_required') ?: 'Please select whether you recommend this business.',
        ]);

        $this->step = 2;
    }

    public function goToStep3()
    {
        $rules = [];
        $messages = [];
        $ratingReqMsg = static_text('rating_required_error') ?: 'Rating is required.';
        foreach ($this->criteria as $criterion) {
            $rules['criteriaRatings.' . $criterion['id']] = 'required|integer|min:1|max:5';
            $messages['criteriaRatings.' . $criterion['id'] . '.min'] = $ratingReqMsg;
            $messages['criteriaRatings.' . $criterion['id'] . '.required'] = $ratingReqMsg;
        }

        if (!empty($rules)) {
            $this->validate($rules, $messages);
        }
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
        $this->step = 6;
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

        $rawLocale = getCurrentLocale() ?: 'en-us';
        $submittedLocale = \App\Services\ReviewModerationService::normalizeLocale($rawLocale);

        $existingReview = Review::where('user_id', Auth::id())
            ->where('business_id', $this->businessId)
            ->first();

        if ($existingReview) {
            $this->reviewId = $existingReview->id;

            \Illuminate\Support\Facades\DB::transaction(function () use ($existingReview, $avg_rating, $lang_id, $submittedLocale) {
                $existingReview->update([
                    'rating'                => $avg_rating,
                    'recommend'             => (bool)$this->recommend,
                    'status'                => Review::STATUS_PROCESSING,
                    'submitted_locale'      => $submittedLocale,
                    'laravel_check_status'  => null,
                    'laravel_fail_reason'   => null,
                    'moderation_api_passed' => null,
                    'localio_ai_status'     => Review::AI_STATUS_WAITING,
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

                // Invalidate existing translations because review content changed
                \App\Services\ReviewTranslationService::invalidateTranslationsForReview($existingReview);
            });

            // Dispatch moderation after transaction commits
            try {
                \App\Jobs\ProcessReviewModeration::dispatch($existingReview->id);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Error dispatching review moderation: ' . $e->getMessage());
            }
            return;
        }

        $review = null;
        \Illuminate\Support\Facades\DB::transaction(function () use (&$review, $lang_id, $avg_rating, $submittedLocale) {
            $review = Review::create([
                'user_id'          => Auth::id(),
                'business_id'      => $this->businessId,
                'lang_id'          => $lang_id,
                'rating'           => $avg_rating,
                'recommend'        => (bool)$this->recommend,
                'status'           => Review::STATUS_PROCESSING,
                'submitted_locale' => $submittedLocale,
                'localio_ai_status'=> Review::AI_STATUS_WAITING,
            ]);

            // Save individual criteria ratings
            foreach ($this->criteriaRatings as $criteriaId => $ratingVal) {
                \App\Models\ReviewRating::create([
                    'review_id'   => $review->id,
                    'criteria_id' => $criteriaId,
                    'rating'      => $ratingVal
                ]);
            }

            // Create translation BEFORE dispatching moderation
            $review->translations()->create([
                'business_id' => $this->businessId,
                'language_id' => $lang_id,
                'title'       => $this->title2,
                'description' => $this->comment,
            ]);

            // Sync selected pros and cons
            $selectedIds = array_merge($this->selectedPros, $this->selectedCons);
            $review->selectedProCons()->sync($selectedIds);
        });

        $this->reviewId = $review->id;

        // Dispatch automated 3-layer review moderation AFTER translations are saved
        try {
            \App\Jobs\ProcessReviewModeration::dispatch($review->id);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error dispatching review moderation: ' . $e->getMessage());
        }

        // Send notifications
        $biz = \App\Models\Business::find($this->businessId);
        $bizName = $biz?->translations?->first()?->name ?? $biz?->name ?? 'Business';
        $currentUser = Auth::user();

        if ($currentUser && $currentUser->email) {
            MailService::sendTemplate('review_submitted_user', $currentUser->email, [
                'user_name'     => $currentUser->first_name ?? 'User',
                'business_name' => $bizName,
                'rating'        => $avg_rating,
            ], $lang_id, $currentUser);
        }

        $adminEmail = config('mail.from.address');
        if ($adminEmail) {
            MailService::sendTemplate('review_submitted_admin', $adminEmail, [
                'user_name'        => trim(($currentUser?->first_name ?? '') . ' ' . ($currentUser?->last_name ?? '')),
                'business_name'    => $bizName,
                'rating'           => $avg_rating,
                'admin_review_url' => url('/admin-dashboard/reviews'),
            ], 1);
        }

        // Notify business vendor if exists
        $vendor = \App\Models\User::where('business_id', $this->businessId)->where('user_type', 'vendor')->first();
        if ($vendor && $vendor->email) {
            $bizSlug = $biz?->translations?->first()?->slug ?? '';
            $reviewUrl = $bizSlug ? route('user.product_detail', ['locale' => app()->getLocale(), 'id' => $bizSlug]) : url('/');
            MailService::sendTemplate('business_new_review', $vendor->email, [
                'business_name' => $bizName,
                'reviewer_name' => $currentUser?->first_name ?? 'A user',
                'rating'        => $avg_rating,
                'review_url'    => $reviewUrl,
            ], $vendor->country_id ?? 1, $vendor);
        }
    }

    protected function fetchProCons(string $type, array $selectedIds, string $searchQuery, int $limit = 30): array
    {
        if (empty($this->categoryIds)) {
            return [];
        }

        $query = CategoryProCon::query()
            ->select(['id', 'text', 'aliases'])
            ->whereIn('category_id', $this->categoryIds)
            ->where('type', $type)
            ->where('status', 1);

        if (!empty($selectedIds)) {
            $query->whereNotIn('id', $selectedIds);
        }

        $trimmed = trim(strtolower($searchQuery));

        if (empty($trimmed)) {
            return $query->orderBy('id')
                ->limit($limit)
                ->get()
                ->map(function ($item) {
                    return [
                        'id'            => $item->id,
                        'text'          => $item->text,
                        'matched_alias' => null,
                    ];
                })
                ->toArray();
        }

        $words = array_values(array_filter(explode(' ', $trimmed), fn($w) => strlen($w) > 0));

        $query->where(function ($q) use ($trimmed, $words) {
            $q->where('text', 'LIKE', "%{$trimmed}%")
              ->orWhere('aliases', 'LIKE', "%{$trimmed}%");

            if (count($words) > 1) {
                $q->orWhere(function ($allWordsQuery) use ($words) {
                    foreach ($words as $w) {
                        $allWordsQuery->where(function ($sub) use ($w) {
                            $sub->where('text', 'LIKE', "%{$w}%")
                                ->orWhere('aliases', 'LIKE', "%{$w}%");
                        });
                    }
                });
            }
        });

        $results = $query->orderBy('id')->limit($limit)->get();

        return $results->map(function ($item) use ($trimmed, $words) {
            $text = strtolower($item->text ?? '');
            $aliases = $item->aliases ?? [];
            if (is_string($aliases)) {
                $aliases = json_decode($aliases, true) ?: [$aliases];
            }
            if (!is_array($aliases)) {
                $aliases = [];
            }

            $matchedAlias = null;
            if (!str_contains($text, $trimmed)) {
                foreach ($aliases as $alias) {
                    $aliasStr = strtolower(trim((string)$alias));
                    if (empty($aliasStr)) continue;

                    if (str_contains($aliasStr, $trimmed) || str_contains($trimmed, $aliasStr)) {
                        $matchedAlias = $alias;
                        break;
                    }

                    if (count($words) > 1) {
                        $allMatch = true;
                        foreach ($words as $w) {
                            if (!str_contains($aliasStr, $w)) {
                                $allMatch = false;
                                break;
                            }
                        }
                        if ($allMatch) {
                            $matchedAlias = $alias;
                            break;
                        }
                    }
                }
            }

            return [
                'id'            => $item->id,
                'text'          => $item->text,
                'matched_alias' => $matchedAlias,
            ];
        })->toArray();
    }

    public function getFilteredProsProperty()
    {
        return $this->fetchProCons('pro', $this->selectedPros, (string)$this->proSearch, $this->proLimit);
    }

    public function getFilteredConsProperty()
    {
        return $this->fetchProCons('con', $this->selectedCons, (string)$this->conSearch, $this->conLimit);
    }

    public function getSelectedProsModelsProperty()
    {
        if (empty($this->selectedPros)) {
            return collect();
        }
        return CategoryProCon::whereIn('id', $this->selectedPros)->get(['id', 'text']);
    }

    public function getSelectedConsModelsProperty()
    {
        if (empty($this->selectedCons)) {
            return collect();
        }
        return CategoryProCon::whereIn('id', $this->selectedCons)->get(['id', 'text']);
    }

    public function render()
    {
        $filteredPros = $this->getFilteredProsProperty();
        $filteredCons = $this->getFilteredConsProperty();

        return view('livewire.add-review', [
            'filteredPros'       => $filteredPros,
            'filteredCons'       => $filteredCons,
            'selectedProsModels' => $this->getSelectedProsModelsProperty(),
            'selectedConsModels' => $this->getSelectedConsModelsProperty(),
            'hasMorePros'        => count($filteredPros) >= $this->proLimit,
            'hasMoreCons'        => count($filteredCons) >= $this->conLimit,
        ]);
    }
}