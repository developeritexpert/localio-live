@extends('user_layout.master')
@section('content')

@livewire('category-page', ['slug' => $slug, 'feature_slug' => $feature_slug ?? null, 'initialPage' => $page ?? 1], key('category-'.$slug.'-'.($feature_slug ?? 'all')))
@endsection
