@extends('admin_layout.master')

@section('title', content: 'Edit Exclusive Deal')

@section('content')
    <livewire:admin.exclusive-deal-form :dealId="$dealId" />
@endsection