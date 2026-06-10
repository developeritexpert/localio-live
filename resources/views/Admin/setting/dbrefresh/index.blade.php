@extends('admin_layout.master')

@section('content')
    <div class="nk-block nk-block-lg setting-db-refresh ">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Refresh Database</h4>
            </div>
        </div>
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('dbrefresh.refresh') }}" class="form-validate" method="post">
                    @csrf
                    <div class="row g-gs">
                        <!-- Name Field -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label" for="name">Enter Password</label>
                                <div class="form-control-wrap">
                                    <input type="password" class="form-control" id="name" name="password"
                                        placeholder="Enter Password Here" />
                                </div>
                                @error('password')
                                <div class="error text-danger"
                                    style="margin-top: 7px !important;margin-bottom: 9px !important;">
                                    {{ $message }}</div>
                            @enderror
                            </div>
                        </div>
                        <!-- Submit Button -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-lg btn-primary btn-localio">Refresh Database</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
