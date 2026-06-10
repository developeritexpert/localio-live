@section('title', 'Wishlist Product | Localio')

@extends('user_dashboard_layout.master')
@section('content')
    <div class="col-lg-9 p-0">
        <div class="user_content">
            <div class="uer_nm">
                <h1>My Favorites</h1>
            </div>
            @if(isset($wishlistItems) && $wishlistItems->count())



                @foreach ($wishlistItems as $item)
                @php
                    $averageRating = round($item->business->reviews->avg('rating'), 1);
                    $filledStars = floor($averageRating); // e.g., 4
                    $totalReviews = $item->business->reviews->count();
                @endphp
                        <div class="crt_main">
                    <div class="row cart_dv savings_main">

                        <div class="col-lg-9 save-lft p-0 ">
                            <div class="crt-lft-top d-flex">
                                <div class="cart_img crt-lft-img">
                                    <img src="{{ asset($item->business->icon_id) }}" class="img-fluid">
                                </div>
                                <div class="cart_text">
                                    <h3>{{ $item->business->translations->first()->name }}</h3>
                                    <div class="crt-ratings d-flex">
                                        <div class="star-p-txt d-flex">
                                            <p>{{ $averageRating > 0 ? $averageRating : '0.0' }}</p>
                                            <div class="star-div d-flex">
                                                <div class="stars">
                                                    <ul class="list-unstyled m-0 d-flex">
                                                        @for ($i = 0; $i < 5; $i++)
                                                            <li>
                                                                <i class="fas fa-star{{ $i < $filledStars ? ' filled' : '' }}"></i>
                                                            </li>
                                                        @endfor
                                                    </ul>
                                                </div>
                                                <i class="fa-solid fa-chevron-down arrw-down"></i>
                                            </div>
                                        </div>
                                        <div class="rate-div">
                                            <p>{{ $totalReviews }} rating{{ $totalReviews !== 1 ? 's' : '' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="crt-lft-btm">
                                <p class="m-0">{!! $item->business->translations->first()->description !!}</p>
                            </div>
                        </div>
                        <div class="col-lg-3 crt_ryt save-rgt p-0">
                            @php
                            $matchedPrice = collect($startingprice)->firstWhere('business.id', $item->business->id);
                            $price = $matchedPrice['starting_price'] ?? null;
                            $amount = $price['amount'] ?? null;
                            $currency = $price['currency'] ?? '$';
                            $timeUnit = ucfirst($price['time_unit'] ?? 'month');
                        @endphp

                        <div class="start-price">
                            @if ($amount)
                                <h5>Starting Price</h5>
                                <p class="m-0 value-p">
                                    <span>{{ $currency }}{{ number_format($amount, 2) }}</span> / {{ $timeUnit }}
                                </p>
                            @else
                                <h5>Starting Price</h5>
                                <p class="m-0 value-p"><span>N/A</span></p>
                            @endif
                        </div>
                            <div class="visit-btn">
                                <a class="unq_btn d-flex g-5" href="{{ $item->business->affiliate_link }}">
                                    Visit Website
                                    <div class="srrw-svg">
                                        <svg width="16" height="12" viewBox="0 0 16 12" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M1.05376 6.27513C6.87501 6.27513 7.218 6.27513 13.0095 6.27513C13.0194 6.24788 13.0293 6.22063 13.0293 6.19338C12.8907 6.1298 12.7521 6.06621 12.6135 5.99354C10.7325 5.05794 9.70292 3.57732 9.31682 1.68793C9.29702 1.61527 9.38612 1.52443 9.42572 1.43359C9.51482 1.4881 9.65342 1.53351 9.68312 1.61527C9.84152 2.04219 9.92072 2.49637 10.1088 2.90513C10.9602 4.77635 12.4749 5.88454 14.6629 6.22063C14.8015 6.2388 14.9005 6.41139 15.0391 6.52039C14.2669 6.75656 13.5838 6.89282 12.9798 7.15624C11.2176 7.92834 10.1979 9.25454 9.78212 10.9895C9.73262 11.2075 9.76232 11.5436 9.30692 11.3983C9.66332 9.25454 10.4751 8.2281 13.0986 6.64756C12.8214 6.64756 12.6333 6.64756 12.4452 6.64756C6.9903 6.64756 6.974 6.64756 1.50916 6.63848C1.30126 6.63848 0.96466 6.75656 1.05376 6.27513Z"
                                                fill="white" stroke="white" stroke-width="0.8" />
                                        </svg>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="cross-icon" onclick="removeProduct({{ $item->id }}, this)">
                            <img src="{{ asset('user-dashboard-theme/img/cross-icon.svg') }}" alt="">
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <div class="crt_main" style="display:flex;justify-content:center;align-items:center;flex-direction:column;">
                    <img src="{{ dashboardDefaultImage() }}" alt="No Favorites" class="img-fluid mb-3" style="width: 280px; height: 280px;">
                    <h5 class="text-muted">You haven't added any favorites yet.</h5>
                </div>
                @endif
        </div>
    </div>
        <script>
            function removeProduct(id, element) {
                let locale = window.location.pathname.split('/')[1]; // Get locale dynamically
                let url = `/${locale}/wishlist/${id}`;

                console.log("Attempting DELETE request to:", url); // Debugging URL

                $.ajax({
                    url: `/${locale}/wishlist/${id}`,
                    type: 'POST', // Use POST instead of DELETE
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: 'DELETE' // Laravel will treat this as DELETE
                    },
                    success: function(response) {
                        console.log("Response from server:", response);

                        if (response.error) {
                            console.error("Error:", response.error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: response.error,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        } else {
                            console.log("Successfully deleted item:", id);
                            $(element).closest('.cart_dv').remove(); // Remove the item from UI

                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Product removed from wishlist.',
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Something went wrong, please try again.',
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });
            }
        </script>
    @endsection
