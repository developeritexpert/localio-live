@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">
                    {{ isset($pricing_data) ? 'Update Pricing Option' : 'Add Pricing Option' }}
                </h4>
            </div>
            <div>
            </div>
        </div>
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('priceoptionsAddprocess') }}" class="form-validate" novalidate="novalidate" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="pricing_option_id" value="{{isset($pricing_data)?$pricing_data['id']:''}}" />
                    <div class="row g-gs">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="name">Name</label>
                                <div class="form-control-wrap">
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{isset($pricing_data)?$pricing_data->translations->first()->name :old('name') }}" />
                                </div>
                                @error('name')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label d-block" for="status-toggle">Status</label>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="status-toggle" name="status"
                                        {{ isset($pricing_data) ? ($pricing_data->status ? 'checked' : '') : 'checked' }}>
                                    <label class="custom-control-label" for="status-toggle" id="status-label">
                                        {{ isset($pricing_data) && !$pricing_data->status ? 'Inactive' : 'Active' }}
                                    </label>
                                </div>
                                @error('status')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-12 mt-5">
                            <div class="form-group">
                                <button type="submit" class="btn btn-lg btn-primary btn-localio">{{isset($pricing_data)?'Update Pricing Option':'Save Pricing Option' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('status-toggle');
            const label = document.getElementById('status-label');

            function updateLabel() {
                if (toggle.checked) {
                    label.textContent = 'Active';
                } else {
                    label.textContent = 'Inactive';
                }
            }

            toggle.addEventListener('change', updateLabel);

            // Initialize on page load
            updateLabel();
        });
    </script>
@endsection
