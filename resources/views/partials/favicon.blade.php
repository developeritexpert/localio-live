@php
    $favPath = function_exists('getSiteFavicon') ? getSiteFavicon($lang_id ?? null) : ($favicon ?? 'front/img/icon.svg');
    if (empty($favPath) || !file_exists(public_path($favPath))) {
        $favPath = 'front/img/icon.svg';
    }
    $favUrl = asset($favPath);
    $favExt = strtolower(pathinfo($favPath, PATHINFO_EXTENSION));
    $favMime = match($favExt) {
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'ico' => 'image/x-icon',
        default => 'image/x-icon',
    };
@endphp
<!-- Favicon / Icons -->
<link rel="icon" type="{{ $favMime }}" href="{{ $favUrl }}">
@if($favExt === 'svg')
<link rel="alternate icon" type="image/png" sizes="32x32" href="{{ asset('front/img/favicon-32x32.png') }}">
<link rel="alternate icon" type="image/png" sizes="16x16" href="{{ asset('front/img/favicon-16x16.png') }}">
@endif
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('front/img/favicon-32x32.png') }}">
