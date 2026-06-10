@extends('admin_layout.master')
@section('content')
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head d-flex justify-content-between">
            <div class="nk-block-head-content">
                <h4 class="title nk-block-title">Add Article</h4>
            </div>
        </div>

        @if (isset($article))
            @php
                $articletranslation = $article->articleTranslations->first();
            @endphp
        @endif


        <div class="card card-bordered">
            <div class="card-inner">
                <div class="nk-block">

                    <form action="{{ route('admin-expert-guide-store-article') }}" class="form-validate"
                        novalidate="novalidate" method="post" enctype="multipart/form-data">
                        @csrf
                        @php
                            // print_r($translation->content);
                        @endphp
                        @if (isset($article))
                            <input type="hidden" name="article_id" value="{{ $article->id }}">
                        @endif
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="preview_title">Preview Title</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" name="preview_title" id="preview_title"
                                            value= "{{ old('preview_title', $articletranslation->preview_title ?? '') }}">
                                    </div>
                                    @error('preview_title')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="preview_description">Preview Description</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" name="preview_description"
                                            id="preview_description"
                                            value= "{{ old('preview_description', $articletranslation->preview_description ?? '') }}">
                                    </div>
                                    @error('preview_description')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="title">Article Title</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control" name="title" id="title"
                                            value= "{{ old('title', $articletranslation->title ?? '') }}">
                                    </div>
                                    @error('title')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label"> Article Categories</label>

                                    <div class="form-control-wrap">
                                        <select class="form-select js-select2" name="category_id" data-search="on">
                                            <option disabled selected>Default Option</option>
                                            @if (isset($categories) && $categories->isNotEmpty())
                                                @foreach ($categories as $category)
                                                    @php
                                                        $categorytranslation = $category->expertGuideCategoryTranslation->first();
                                                        $selectedCategoryId = old(
                                                            'category_id',
                                                            $article->category_id ?? null,
                                                        );
                                                    @endphp

                                                    @if ($categorytranslation)
                                                        <option value="{{ $category->id }}"
                                                            {{ $selectedCategoryId == $category->id ? 'selected' : '' }}>
                                                            {{ $categorytranslation->name ?? 'Unnamed Category' }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    @error('category_id')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="description">Description</label>
                                    <div class="form-control-wrap">
                                        <textarea class="description" name="description" id="description" rows="4" cols="50">{{ old('description', $articletranslation->description ?? '') }}</textarea>
                                    </div>
                                    @error('description')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div id="dynamic-content-wrapper">
                                @php
                                $lang_id=getCurrentLanguageID();
                                $contents = isset($article) && $article->contents ? $article->contents->where('lang_id',$lang_id) : collect([]);
                            @endphp
                                @foreach ($contents as $index => $block)
                                    <div class="content-block border p-3 mb-3">
                                        <div class="form-group">
                                            <label>Section Title</label>
                                            <input type="text" name="contents[{{ $index }}][section_title]"
                                                class="form-control"
                                                value="{{ old("contents.$index.section_title", $block->section_title) }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Section Content</label>
                                            <textarea name="contents[{{ $index }}][section_content]" class="section_content form-control section-editor"
                                                rows="4">{{ old("contents.$index.section_content", $block->section_content) }}</textarea>
                                        </div>
                                        <button type="button"
                                            class="btn btn-danger btn-sm remove-content-block">Remove</button>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="btn btn-outline-primary" id="add-content-block">+ Add More
                                Content</button>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="image">Upload Image</label>
                                    <div class="dz-message">
                                        <input type="file" class="form-control" name="image" id="image">
                                    </div>
                                    @if ($article)
                                        @if (isset($article->image))
                                            <img src="{{ asset($article->image) }}" alt="{{ $article->name }}"
                                                style="width: 50px; height: auto;">
                                        @else
                                            <span>No Image</span>
                                        @endif
                                    @endif
                                    @error('image')
                                        <div class="error text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12 mt-4">
                                <div class="form-group">
                                    <button class="addCategory btn btn-primary  text-center"><em
                                            class=""></em><span>Submit</span></button>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        let contentIndex = {{ count($contents) }};

        // Initialize CKEditor for a specific element
        function initCKEditorForElement(element) {
            ClassicEditor
                .create(element)
                .then(editor => {
                    element.classList.add('ckeditor-initialized');
                    element.ckeditorInstance = editor; // optional reference
                })
                .catch(error => {
                    console.error(error);
                });
        }

        // Initialize CKEditor on all uninitialized textareas
        function initAllCKEditors() {
            const editors = document.querySelectorAll('.section_content');
            editors.forEach(el => {
                if (!el.classList.contains('ckeditor-initialized')) {
                    initCKEditorForElement(el);
                }
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function () {
            initAllCKEditors();
        });

        // Add More Content
        document.getElementById('add-content-block').addEventListener('click', function () {
            const wrapper = document.getElementById('dynamic-content-wrapper');
            const html = `
            <div class="content-block border p-3 mb-3">
                <div class="form-group">
                    <label>Section Title</label>
                    <input type="text" name="contents[${contentIndex}][section_title]" class="form-control">
                </div>
                <div class="form-group">
                    <label>Section Content</label>
                    <textarea name="contents[${contentIndex}][section_content]" class="form-control section_content section-editor" rows="4"></textarea>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-content-block mt-2">Remove</button>
            </div>`;
            wrapper.insertAdjacentHTML('beforeend', html);
            initAllCKEditors();
            contentIndex++;
        });

        // Remove Content Block (and destroy CKEditor if needed)
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-content-block')) {
                const block = e.target.closest('.content-block');
                if (block) {
                    const textarea = block.querySelector('.section_content');
                    if (textarea && textarea.ckeditorInstance) {
                        textarea.ckeditorInstance.destroy();
                    }
                    block.remove();
                }
            }
        });
    </script>
@endsection
