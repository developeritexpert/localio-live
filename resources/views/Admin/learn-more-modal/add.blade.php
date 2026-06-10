@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">
                    {{ isset($section) ? 'Update Learn More Content' : 'Add Learn More Content' }}
                </h4>
            </div>
        </div>
        <div class="card card-bordered">
            <div class="card-inner">
                <form action="{{ route('modalstoreOrUpdate') }}" method="POST" class="form-validate">
                    @csrf
                    <input type="hidden" name="lang_id" value="{{ $langId ?? 1 }}">

                    <div id="dynamicSections">
                        @php
                        $existingSections = collect(isset($section) ? [$section] : (isset($sections) ? $sections : []));
                    @endphp
                        @if($existingSections->isNotEmpty())
                            @foreach($existingSections as $index => $sectionItem)
                                <div class="section-block border p-3 mb-3" data-index="{{ $index }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">Section {{ $index + 1 }}</h6>
                                        @if($index > 0)
                                            <button type="button" class="btn btn-danger btn-sm remove-section">Remove Section</button>
                                        @endif
                                    </div>

                                    @if(isset($sectionItem->id))
                                        <input type="hidden" name="sections[{{ $index }}][id]" value="{{ $sectionItem->id }}">
                                    @endif

                                    <div class="form-group">
                                        <label class="form-label">Title <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control"
                                               name="sections[{{ $index }}][title]"
                                               value="{{ old('sections.'.$index.'.title', $sectionItem->title ?? '') }}"
                                               required>
                                        @error('sections.'.$index.'.title')
                                            <span class="invalid text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Content <span class="text-danger">*</span></label>
                                        <textarea name="sections[{{ $index }}][content]"
                                                  class="form-control"
                                                  rows="4"
                                                  required>{{ old('sections.'.$index.'.content', $sectionItem->content ?? '') }}</textarea>
                                        @error('sections.'.$index.'.content')
                                            <span class="invalid text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="section-block border p-3 mb-3" data-index="0">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Section 1</h6>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control"
                                           name="sections[0][title]"
                                           value="{{ old('sections.0.title') }}"
                                           required>
                                    @error('sections.0.title')
                                        <span class="invalid text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Content <span class="text-danger">*</span></label>
                                    <textarea name="sections[0][content]"
                                              class="form-control"
                                              rows="4"
                                              required>{{ old('sections.0.content') }}</textarea>
                                    @error('sections.0.content')
                                        <span class="invalid text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-secondary" id="addSectionBtn">
                            <em class="icon ni ni-plus"></em> Add Section
                        </button>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($section) ? 'Update Content' : 'Save Content' }}
                        </button>
                        <a href="{{ route('learn-modal') }}" class="btn btn-outline-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let sectionIndex = {{ isset($section) ? 1 : (isset($sections) ? count($sections) : 1) }};

    document.getElementById('addSectionBtn').addEventListener('click', function () {
        const container = document.getElementById('dynamicSections');
        const html = `
            <div class="section-block border p-3 mb-3" data-index="${sectionIndex}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Section ${sectionIndex + 1}</h6>
                    <button type="button" class="btn btn-danger btn-sm remove-section">Remove Section</button>
                </div>

                <div class="form-group">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="sections[${sectionIndex}][title]" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Content <span class="text-danger">*</span></label>
                    <textarea name="sections[${sectionIndex}][content]" class="form-control" rows="4" required></textarea>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        sectionIndex++;
        updateSectionNumbers();
    });

    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-section')) {
            e.target.closest('.section-block').remove();
            updateSectionNumbers();
        }
    });

    function updateSectionNumbers() {
        const sections = document.querySelectorAll('.section-block');
        sections.forEach((section, index) => {
            const title = section.querySelector('h6');
            title.textContent = `Section ${index + 1}`;
        });
    }
</script>
@endpush
