@props(['business' => null, 'name' => null, 'icon' => null, 'class' => '', 'imgClass' => '', 'size' => null, 'style' => '',])

@php
    $bizName = $name;
    if (empty($bizName) && $business) {
        if (isset($business->translations) && is_iterable($business->translations) && count($business->translations) > 0) {
            $bizName = $business->translations->first()->name ?? '';
        } else {
            $bizName = $business->name ?? '';
        }
    }
    $bizName = trim($bizName ?? '');
    $initial = !empty($bizName) ? mb_strtoupper(mb_substr($bizName, 0, 1)) : 'B';

    $logoSrc = $icon;
    if (empty($logoSrc) && $business) {
        $logoSrc = $business->getRawOriginal('icon_id') ?? $business->icon_id ?? null;
    }

    $isDefaultLogo = empty($logoSrc) || in_array($logoSrc, [
        'front/img/default_business_logo.svg',
        'front/img/logo.svg',
        'front/img/default.png',
        'images/default.png',
        'front/img/top-rate-img2.svg',
        'front/img/big-asana.png',
        'front/img/sftare-img1.svg',
        'front/img/poplr-zero.svg',
        'front/img/lyt-rd-grey.svg'
    ]);

    $hasLogo = !$isDefaultLogo && !empty($logoSrc);
    $sizeClass = $size ? 'size-' . $size : '';
@endphp

@if ($hasLogo)
    <img src="{{ asset($logoSrc) }}" alt="{{ $bizName }}" class="{{ $imgClass }} {{ $class }}" @if($style) style="{{ $style }}" @endif>
@else
    <div class="business-initial-logo {{ $sizeClass }} {{ $class }}" @if($style) style="{{ $style }}" @endif aria-label="{{ $bizName }}">
        {{ $initial }}
    </div>
@endif
