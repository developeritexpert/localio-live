@extends('user_layout.master')
@section('content')
@livewire('top-rated-product', ['initialPage' => $page ?? 1, 'category' => $category ?? null])
@endsection
