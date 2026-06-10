@extends('admin_layout.master')
@section('content')

    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content review-title">
                <h4 class="title nk-block-title">Add Review : {{ strtolower(getCurrentLocale()) }}</h4>
            </div>
        </div>


        <div class="card card-bordered review-section">
            <div class="card-inner review-inner">
                <div class="nk-block">
                    <form action="{{ url('admin-dashboard/review-add-procc') ?? '' }}" class="form-validate"
                        novalidate="novalidate" method="POST">
                        @csrf


                        <div class="row g-3 form-row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="name">Rating</label>
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <input type="number" class="form-control"
                                                name="rating" id="rating" value="">
                                        </div>
                                    </div>
                                    @error('rating')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            @if (isset($businesses))
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Business Name</label>
                                        <div class="form-control-wrap">
                                            <select class="form-select js-select2" name="business_id" data-search="on">
                                                @foreach ($businesses as $busines)
                                                    <option value="{{ $busines->id }}"> {{ $busines->translations->first()->name ?? ''}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        @error('business_id')
                                            <div class="error text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-12 mt-3">
                                <div class="form-group">
                                    <label class="form-label" for="public_name">Public Name </label>
                                    <div class="form-control-wrap">
                                   <input type="text" class="form-control" name="public_name" id="public_name">
                                        {{-- <textarea class="form-control" name="title" id="title" rows="2" cols="79"
                                            style="text-align: left;" placeholder="Enter review title"></textarea> --}}
                                    </div>
                                    @error('public_name')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <div class="form-group">
                                    <label class="form-label" for="title">Title</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control" name="title" id="title" rows="2" cols="79"
                                            style="text-align: left;" placeholder="Enter review title"></textarea>
                                    </div>
                                    @error('title')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <div class="form-group">
                                    <label class="form-label" for="description">Description</label>
                                    <div class="form-control-wrap">
                                        <textarea class="form-control" name="description" id="description" rows="4" cols="79"
                                            style="text-align: left;" placeholder="Enter review description"></textarea>
                                    </div>
                                    @error('description')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="form-label d-block text-left">Review Status</label>
                                    <div class="d-flex align-items-center justify-content-left">
                                        <!-- Private Label -->
                                        <label class="mb-0" style="margin-right: 20px;"><b>Awating Approval</b></label>

                                        <!-- Toggle Switch -->
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="customSwitch1">
                                            <label class="custom-control-label" for="customSwitch1"></label>
                                        </div>

                                        <!-- Public Label -->
                                        <label class="ml-3 mb-0"><b>Approved</b></label>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden Input to Store Status Value -->
                            <input type="hidden" name="status" id="statusHidden1" value="active">

                            @error('status')
                                <div class="error text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                </div>


                <div class="col-md-12 mt-4">
                    <div class="form-group">
                        <button class="addCategory btn btn-primary  text-center review-btn btn-localio"><em
                                class=""></em><span>Save Review</span></button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    <script>
        $(document).ready(function() {
            const switchInput = $("#customSwitch1");
            const hiddenInput = $("#statusHidden1");

            // Check if script is running
            console.log("Script loaded!");

            // Set checkbox state based on hidden input value
            let initialStatus = hiddenInput.val();
            console.log("Initial Status:", initialStatus);
            switchInput.prop("checked", initialStatus === "active");

            // Update hidden input when the switch is toggled
            switchInput.on("change", function() {
                let newStatus = this.checked ? "active" : "inactive";
                hiddenInput.val(newStatus);
                console.log("Updated Status:", newStatus);
            });
        });
    </script>

@endsection
