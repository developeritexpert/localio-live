@extends('user_layout.master')
@section('content')

@livewire('category-page', ['slug' => $slug], key('category-'.$slug))
@endsection
