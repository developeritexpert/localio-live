@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Add {{ ucfirst(str_replace('_', ' ', request('tab'))) }}</h4>
            </div>
        </div>
        <?php $lang = getCurrentLocale(); ?>
        <div class="card card-bordered">
            <div class="card-inner">
                <div class="nk-block">
                    <form action="{{ route('productfeature.add-process') }}" class="form-validate" novalidate="novalidate"
                        method="post">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="name">Business Feature</label>
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <input type="hidden" name="tab" value="{{ request('tab') }}">
                                            <input type="text" class="form-control" name="name" id="name"
                                                placeholder="Business Feature" value="">
                                        </div>
                                    </div>
                                    @error('name')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <button class="addCategory btn btn-primary text-center btn-localio"><em
                                        class=""></em>Save Feature</button>
                            </div>
                        </div>
                </div>
            </div>
            </form>
        </div>
    </div>
    </div>
    </div>
@endsection
