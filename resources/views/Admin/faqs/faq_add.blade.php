    @extends('admin_layout.master')
    @section('content')
        <div class="nk-block nk-block-lg">
            <div class="nk-block-head d-flex justify-content-between">
                <div class="nk-block-head-content">
                    <h4 class="title nk-block-title">Add FAQ</h4>
                </div>
            </div>

            <div class="card card-bordered">
                <div class="card-inner">
                    {{-- @php
                         dd($faq->type );
                    @endphp --}}
                    <div class="nk-block">
                        <form action="{{ url('admin-dashboard/faq-add-procc') ?? '' }}" class="form-validate"
                            novalidate="novalidate" method="post">
                            @csrf
                            @if(isset($faq))
                                <input type="hidden" name="faq_id" value="{{ $faq->id }}">
                            @endif
                            <input type="hidden" name="lang_code" value="{{ getCurrentLanguageID() }}" />

                            {{-- <input type="hidden" name="faq_id" value="{{ isset($faqTranslation) ? $faqTranslation->faq_id : '' }}"> --}}
                            <div class="col-md-12 mt-3">
                                <div class="form-group">
                                    <label class="form-label" for="faq_category">Faq Category</label>
                                    <select class="form-control" name="faq_category">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                @if((isset($faq) && $faq->faqs_category_id == $category->id) || old('faq_category') == $category->id)
                                                    selected
                                                @endif>
                                                {{ $category->translation?->name ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('faq_category')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12 mt-3">
                                <div class="form-group">
                                    <label for="type">FAQ Type</label>
                                    <select name="type" class="form-control" required>
                                        @php
                                            $selectedType = old('type') ?? ($faq->type ?? null);
                                            // dd($faq->type );
                                        @endphp
                                        <option value="user" {{ $selectedType === 'user' ? 'selected' : '' }}>User</option>
                                        <option value="vendor" {{ $selectedType === 'vendor' ? 'selected' : '' }}>Vendor</option>
                                    </select>
                                    @error('type')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror

                                </div>
                            </div>



                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label" for="name">Question</label>
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control" name="question" id="question"
                                                    value="{{ isset($faqTranslation) ? $faqTranslation->question : $faq->question ?? '' }}">
                                            </div>
                                        </div>
                                        @error('question')
                                            <div class="error text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <div class="form-group">
                                        <label class="form-label" for="description">Answer</label>
                                        <div class="form-control-wrap">
                                            <textarea class="description" name="answer" id="answer" rows="4" cols="50">{{ strip_tags(isset($faqTranslation) ? $faqTranslation->answer : $faq->answer ?? '') }}</textarea>
                                        </div>
                                        @error('answer')
                                            <div class="error text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-12 mt-4">
                                <div class="form-group">
                                    <button class="addCategory btn btn-primary btn-localio  text-center"><em
                                            class=""></em><span>{{ isset($faq) ? 'Update Faq' : 'Save Faq' }}</span></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        {{-- </div> --}}

    @endsection
