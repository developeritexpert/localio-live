<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Review;
use App\Models\WebSetting;

class LatestReviews extends Component
{
    public $title = null;

    public function render()
    {
        $lang_id = getCurrentLanguageID();
        $default_image = WebSetting::where('key', 'user_default_image')->value('value') ?? 'front/img/default.png';

        $latestReviews = Review::with([
            'user',
            'business.translations' => fn($q) => $q->where('lang_id', $lang_id),
            'business.reviews.translations' => fn($q) => $q->where('language_id', $lang_id),
            'translations' => fn($q) => $q->where('language_id', $lang_id),
        ])
            ->whereHas('user', fn($q) => $q->where('user_type', '!=', 'admin'))
            ->where('status','active')
            ->latest()
            ->get();

        return view('livewire.latest-reviews', [
            'latestReviews' => $latestReviews,
            'default_image' => $default_image,
            'lang_id' => $lang_id,
        ]);
    }
}
