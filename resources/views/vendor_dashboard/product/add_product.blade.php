@extends('vendor_dashboard_layout.master')

@section('content')
@livewire('vendor.add_product', ['productId' => $product_id])
@endsection