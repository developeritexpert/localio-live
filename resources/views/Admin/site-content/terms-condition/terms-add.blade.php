@extends('admin_layout.master')
@section('content')
<div class="nk-block nk-block-lg">
    <div class="nk-block-head d-flex justify-content-between">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title">Add Terms</h4>
        </div>
    </div>

    <div class="card card-bordered">
        <div class="card-inner">
            <div class="nk-block">
                <form action="{{ route('terms_add_process') ?? '' }}" class="form-validate" method="POST">
                    @csrf
                    <div class="card border">
                        <div class="card-body">
                            <div class="form-group col-lg-12">
                                <label class="form-label" for="contact_heading">Title</label>
                                <div class="form-control-wrap">
                                    <input type="hidden" name="term_id" value="{{ isset($terms_data) ? $terms_data['terms_id']: '' }}" />
                                    <input type="text" class="form-control" id="contact_heading" name="title"
                                        value="{{ isset($terms_data) ? $terms_data['title']: '' }}" placeholder="Title" />
                                </div>

                                @error('title')
                                    <p class="text-danger">{{$message}}</p>
                                @enderror
                            </div>

                           <div class="form-group col-lg-12">
                                <label class="form-label" for="contact_heading">Description</label>
                                <div class="form-control-wrap">
                                    <textarea name="policy_description" class="description" id="" cols="50" rows="2"  placeholder="Enter Description">
                                        {{isset($terms_data) ? $terms_data['description'] : ''}}
                                    </textarea>
                                 </div>
                                 @error('policy_description')
                                    <p class="text-danger">{{$message}}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12 mt-4">
                            <div class="form-group">
                                <button class="addCategory btn btn-primary  text-center btn-localio"><em
                                        class=""></em><span>Save Content</span></button>
                            </div>
                        </div>
                    </div>
                </form>
    </div>
</div>
</div>
</div>

@endsection
