@extends('user_layout.master')
@section('meta_title', isset($categoriesContents['meta_title']) && isset($categoriesContents['meta_title']) ? $categoriesContents['meta_title'] : 'Categories')
@section('content')
<style>
    /* Sidebar */
    .parent-cat-sidebar {
        border: 1px solid #e8eef6;
        border-radius: 8px;
        padding: 20px;
        background: #fff;
    }
    .parent-cat-sidebar h4 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 15px;
        color: #002347;
        line-height: 1.3;
    }
    .parent-cat-sidebar ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .parent-cat-sidebar li {
        margin-bottom: 12px;
        /* padding-bottom: 12px; */
        color: #555;
        font-size: 14px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: color 0.2s;
    }
    .parent-cat-sidebar li::after {
        content: '›';
        font-size: 18px;
        color: #e56b46;
        margin-left: 5px;
    }
    .parent-cat-sidebar li:hover {
        /* color: #e56b46; */
        text-decoration: underline;
    }
    .parent-cat-sidebar li.active {
        color: #06498b;
        font-weight: 700;
    }
    .parent-cat-sidebar li.cat_loading {
        opacity: 0.6;
    }

    /* Main content */
    .parent-cat-main h1 {
        font-size: 32px;
        font-weight: 700;
        color: #002347;
        margin-bottom: 5px;
    }
    .parent-cat-main > p {
        font-size: 15px;
        color: #666;
        margin-bottom: 20px;
    }
    .parent-cat-main h3 {
        font-size: 24px;
        font-weight: 700;
        color: #002347;
        margin-bottom: 20px;
    }
    .subcat_grid {
        transition: opacity 0.3s ease;
    }
    .subcat_grid.subcat_fade {
        opacity: 0.3;
    }

    /* Subcategory block */
    .subcat-block {
        border: 1px solid #e8eef6;
        border-radius: 8px;
        padding: 20px;
        background: #fff;
        margin-bottom: 25px;
    }
    .subcat-block h4 {
        font-size: 20px;
        font-weight: 700;
        color: #002347;
        margin-bottom: 8px;
    }
    .subcat-block p {
        font-size: 14px;
        color: #555;
        margin-bottom: 15px;
    }
    .subcat-block .subcat-popular-text {
        font-size: 13px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
    }
    .subcat-link:hover {
        text-decoration: underline !important;
    }

    /* Top products grid */
    .top-products-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
    }
    @media(max-width: 991px) {
        .top-products-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media(max-width: 575px) {
        .top-products-grid {
            grid-template-columns: 1fr;
        }
    }
    .top-product-card {
        border: 1px solid #e8eef6;
        border-radius: 10px;
        padding: 16px;
        background: #fff;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    .top-product-card:hover {
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        transform: translateY(-3px);
        border-color: #d1def0;
    }
    .top-product-logo {
        width: 55px;
        height: 55px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .top-product-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }
    .top-product-logo .avatar-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #002347 0%, #00438a 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 22px;
        border-radius: 50%;
        box-shadow: inset 0 2px 5px rgba(0,0,0,0.2), 0 2px 6px rgba(0,0,0,0.05);
    }
    .top-product-info {
        flex: 1;
        min-width: 0;
    }
    .top-product-info h6 {
        font-size: 15px;
        font-weight: 700;
        color: #002347;
        margin: 0 0 5px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: color 0.2s ease;
    }
    .top-product-card:hover .top-product-info h6 {
        color: #e56b46;
    }
    .top-product-rating {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #777;
        margin-bottom: 2px;
    }
    .top-product-stars {
        color: #e56b46;
        font-size: 12px;
        display: flex;
    }
    .top-product-rating-text {
        font-size: 12px;
        color: #888;
        font-weight: 500;
    }

    /* Buttons */
    .btn-view-details {
        border: 1.5px solid #06498b;
        border-radius: 30px;
        background: transparent;
        color: #06498b;
        font-size: 11px;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-view-details:hover {
        background: #06498b;
        color: #fff;
        text-decoration: none;
    }

    /* Loading spinner overlay */
    .subcat_loading_overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 5;
        background: rgba(255,255,255,0.5);
    }
    .subcat_spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #e0e0e0;
        border-top-color: #06498b;
        border-radius: 50%;
        animation: subcat-spin 0.8s linear infinite;
    }
    @keyframes subcat-spin {
        to { transform: rotate(360deg); }
    }
    .sfwr_sec.cat_page_secs {
    padding-top: 50px;
    margin-top: 125px;
}
</style>
{{-- <section class="banner_sec help-cntr-bnr inr-bnr dark cate_bnr_sec" style="background-color: #003F7D;">
    <div class="bubble-wrp">

        @if ($backgroundImage)
        <img src="{{ asset($backgroundImage->meta_value) }}" alt="">
        @else
        <img src="{{ asset('front/img/small-bnnr-bg.png') }}" alt="">
        @endif


    </div>
    <div class="banner_content">
        <div class="container">
            <div class="banner_row" data-aos="fade-up" data-aos-duration="1000">
                <div class="banner_text_col">
                    <div class="banner_content_inner">
                        <h1>{{ $categoriesContents['heading'] ?? '' }}</h1>
                        <p>
                            {{ $categoriesContents['description'] ?? '' }}
                        </p>
                        @livewire('search-bar', ['placeholder' => $homeContents['placeholder_text'] ?? 'Search...'])

                    </div>
                </div>
                <div class="banner_image_col">
                    <div class="banner_image">
                        @if ($headerImage)
                        <img src="{{ asset($headerImage->meta_value) }}" alt="">
                        @else
                        <img src="{{ asset('front/img/ctgry-bannr.png') }}" class="banner_top_image">
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</section> --}}

@livewire('category-sidebar', ['categories' => $categories, 'categoriesContents' => $categoriesContents])

{{-- <section class="sfwr_sec light p_120">
    <div class="container">
        <div class="sfwr_content">
            <!-- <h2 data-aos="zoom-in" data-aos-duration="1000">What type of software are you looking for?</h2> -->
            <h2 data-aos="zoom-in" data-aos-duration="1000">{{ $categoriesContents['main_heading'] ?? '' }}</h2>
            <div class="row gy-4">
                @foreach ($categories as $category)
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-duration="1000">
                    <div class="sfwr_box"
                        onclick="changeCategory('{{ $category->translation()->first()->slug }}')"
                        style="cursor: pointer; transition: box-shadow 0.3s ease;"
                        onmouseover="this.style.boxShadow='0 4px 20px rgba(0, 0, 0, 0.2)'"
                        onmouseout="this.style.boxShadow='none'">
                        <div class="sfwr_hd">
                            <div class="img-name">
                                <div class="sfwr_img">
                                    <img src="{{ asset(($category->imageMedia?->dir_path ?? 'images') . '/' . ($category->imageMedia?->file_name ?? 'default-category.png')) }}" alt="Category Icon">
                                </div>
                            </div>

                            <div class="sfwr_name">
                                <h6 class="big-bld">
                                    {{ $category->translations->name ?? 'No Name' }}
                                </h6>
                            </div>

                            <div class="sfwr_text">
                                <p class="list-unstyled m-0">
                                    {!! $category->translations->description ?? 'No Description Available' !!}
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>
</section> --}}
@livewire('latest-reviews')

<script>
    $(window).on('load', function() {
        $('body').addClass('CatgryPgCls');
    });
</script>
<script>
    public function selectCategory($categoryId)
{
    $this->selectedCategoryId = $categoryId;

    usleep(400000); // 0.4 sec artificial delay — sirf better UX feel ke liye

    $this->subCategories = Category::where('parent_id', $categoryId)
        ->where('status', 1)
        ->with('translations', 'imageMedia')
        ->get();
}
</script>
@endsection
