@extends('admin_layout.master')
@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                @livewire('admin.category-pro-cons-manager')
            </div>
        </div>
    </div>
</div>
@endsection
