@extends('user_layout.master')
@section('content')

@livewire('category-page', ['slug' => $slug, 'initialPage' => $page ?? 1], key('category-'.$slug))
@endsection
