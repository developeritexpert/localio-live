@php
    $lang_id = getCurrentLanguageID();
    $default_image = \App\Models\WebSetting::where('key', 'user_default_image')->value('value') ?? 'front/img/default.png';

    $latestReviews = \App\Models\Review::with([
        'user',
        'business.translations' => fn($q) => $q->where('lang_id', $lang_id),
        'business.reviews.translations' => fn($q) => $q->where('language_id', $lang_id),
        'translations' => fn($q) => $q->where('language_id', $lang_id),
    ])
        ->whereHas('user', fn($q) => $q->where('user_type', '!=', 'admin'))
        ->whereIn('status', ['published', 'active'])
        ->latest()
        ->get();
@endphp
@include('livewire.latest-reviews', [
    'latestReviews' => $latestReviews,
    'default_image' => $default_image,
    'lang_id' => $lang_id,
])

