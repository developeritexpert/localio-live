@extends('admin_layout.master')
@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Add Contributor Badge</h3>
                        </div>
                    </div>
                </div>
                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <form action="{{ route('admin.contributor.badges.store') }}" method="POST">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label" for="name">Badge Name</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="name" name="name" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label" for="min_reviews">Min Reviews</label>
                                            <div class="form-control-wrap">
                                                <input type="number" class="form-control" id="min_reviews" name="min_reviews" required min="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label class="form-label" for="max_reviews">Max Reviews (Leave empty for X+)</label>
                                            <div class="form-control-wrap">
                                                <input type="number" class="form-control" id="max_reviews" name="max_reviews" min="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="form-label" for="icon">Icon (Emoji or HTML)</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" id="icon" name="icon" placeholder="🏅">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Save Badge</button>
                                            <a href="{{ route('admin.contributor.badges') }}" class="btn btn-outline-light">Cancel</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
