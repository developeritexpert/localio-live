@extends('user_layout.master')
@section('content')
@livewire('business-alternatives', ['businessId' => $business->id])
@endsection
