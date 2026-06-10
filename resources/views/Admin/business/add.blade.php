@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">
                    {{ isset($business_data) ? 'Update Business' : 'Add Business' }}
                </h4>
            </div>
        </div>

        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('add-business-process') }}" method="post" enctype="multipart/form-data" class="form-validate">
                    @csrf
                    <input type="hidden" name="business_id" value="{{ isset($business_data) ? $business_data['id'] : '' }}" />

                    <div class="row g-gs">
                        @php
                            $fields = [
                                'name' => 'text',
                                'affiliate_partner' => 'text',
                                'affiliate_link' => 'url',
                                'headquaters' => 'text',
                                'year_found' => 'number',
                                'languages_supported' => 'text',
                            ];
                        @endphp

                        @foreach($fields as $field => $type)
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="{{ $field }}">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                    <input type="{{ $type }}" class="form-control" id="{{ $field }}" name="{{ $field }}"
                                           value="{{ isset($business_data[$field]) ? $business_data[$field] : old($field) }}" />
                                    @error($field)
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endforeach

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="activate_all_countries">Activate All Countries</label>
                                <select class="form-control" name="activate_all_countries" id="activate_all_countries">
                                    <option value="1" {{ isset($business_data['activate_all_countries']) && $business_data['activate_all_countries'] == 1 ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ isset($business_data['activate_all_countries']) && $business_data['activate_all_countries'] == 0 ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="image">Upload Image</label>
                                <input type="file" class="form-control" name="image" id="image">
                                @if(isset($business_image->image_id))
                                    <img src="{{ asset($business_image->image_id) }}" alt="Business Image" class="img-fluid rounded-circle mt-2" style="height: 50px;">
                                @endif
                                @error('image')<div class="error text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <!-- Icon Upload -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="category_icon">Upload Icon</label>
                                <input type="file" class="form-control" name="category_icon" id="categoryIcon">
                                @if(isset($business_image->icon_id))
                                    <img src="{{ asset($business_image->icon_id) }}" alt="Business Icon" class="img-fluid rounded-circle mt-2" style="height: 50px;">
                                @endif
                                @error('category_icon')<div class="error text-danger">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-12 mt-5">
                            <div class="form-group">
                                <button type="submit" class="btn btn-lg btn-primary">{{ isset($business_data) ? 'Update Business' : 'Save Business' }}</button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
