    @extends('admin_layout.master')
    @section('content')

        <?php
        $locale = getCurrentLocale();

        ?>
        <div class="nk-block nk-block-lg reviews-published-reviews">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Review</h3>
                    </div>
                    <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em
                                    class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="datatable-init nowrap nk-tb-list nk-tb-ulist" data-auto-responsive="false">
                        <thead>
                            <tr class="nk-tb-item nk-tb-head">
                                <th class="nk-tb-col"><span class="sub-text">User Name</span></th>
                                {{-- <th class="nk-tb-col"><span class="sub-text">Public Name</span></th> --}}
                                <th class="nk-tb-col"><span class="sub-text">Business Name</span></th>
                                <th class="nk-tb-col"><span class="sub-text">Review Description</span></th>
                                <th class="nk-tb-col"><span class="sub-text">Rating </span></th>
                                <th class="nk-tb-col"><span class="sub-text">Status </span></th>
                                <th class="nk-tb-col tb-tnx-action">
                                    <span>Action</span>
                                </th>
                            </tr>
                        </thead>
                        @if (isset($reviews) && $reviews->isNotEmpty())
                            <tbody>
                                @foreach ($reviews as $review)
                                    <tr class="nk-tb-item">
                                        <td class="nk-tb-col">
                                            <div class="user-card">
                                                <div class="user-info">
                                                    <span class="tb-lead">
                                                        {{ $review->user->first_name ?? '' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        {{-- <td class="nk-tb-col">
                                            <div class="user-card">
                                                <div class="user-info">
                                                    <span class="tb-lead">
                                                        {{ $review->public_name ?? ''}}
                                                    </span>
                                                </div>
                                            </div>
                                        </td> --}}

                                        <td class="nk-tb-col">
                                            <div class="user-card">
                                                <div class="user-info">
                                                    <span class="tb-lead">
                                                        {{ $review->business?->translations->first()->name ?? 'Business not available' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="nk-tb-col tb-col-mb">
                                            <span class="tb-lead">
                                                {{ strip_tags($review->translations->first()->description ?? 'No Description Available') }}
                                            </span>
                                        </td>
                                        <td class="nk-tb-col tb-col-mb">
                                            <div class="d-flex align-items-center star-rating" data-rating-name="average_rating">
                                                @php
                                                    $roundedRating = round($review->rating ?? 0);
                                                @endphp
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="fa {{ $i <= $roundedRating ? 'fa-star' : 'fa-star-o' }} text-warning fs-5 me-1"></i>
                                                @endfor
                                            </div>
                                        </td>


                                        <td class="nk-tb-col tb-col-mb">
                                            @if ($review->status === 'active')
                                                <span class=" tb-lead text-success">Approved</span>
                                            @else
                                                <span class=" tb-lead text-danger">Awaiting Approval</span>
                                            @endif
                                            </span>
                                        </td>
                                        <td class="nk-tb-col nk-tb-col-tools">
                                                    <div class="drodown">
                                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                                            data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                        <div class="dropdown-menu dropdown-menu-end" style="list-style: none; padding: 0; margin: 0; height:auto">
                                                            <ul class="link-list-opt no-bdr" >
                                                                @if ($review->status === 'active')
                                                                    <li>
                                                                        <a
                                                                            href="{{ route('review-status-update',$review->id) }}">
                                                                            <em class="icon ni ni-cross-circle-fill"></em>
                                                                            <span>Reject
                                                                        </a>
                                                                    </li>
                                                                @else
                                                                    <li>
                                                                        <a
                                                                            href="{{ route('review-status-update',$review->id) }}">
                                                                            <em class="icon ni ni-check-circle-fill"></em>
                                                                            <span>Approved</span>
                                                                        </a>
                                                                    </li>
                                                                @endif
                                                                <li>
                                                                    <a href="{{ route('review-edit', ['id' => $review->id]) }}">
                                                                        <em class="icon ni ni-edit-fill"></em>
                                                                        <span>Edit</span>
                                                                    </a>
                                                                </li>

                                                                {{-- Review Translation --}}
                                                                {{-- <li>
                                                                    <a href="{{ route('review-edit', ['id' => $review->id]) }}">
                                                                        <em class="icon ni ni-edit-fill"></em>
                                                                        <span>Translation</span>
                                                                    </a>
                                                                </li> --}}

                                                                <li class="removeConfermation"
                                                                data-url="{{ route('review-delete', $review->id) }}">
                                                                    <a
                                                                        href="{{ route('review-delete', ['id' => $review->id]) }}">
                                                                        <em class="icon ni ni-trash"></em>
                                                                        <span>Delete</span>
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    @endsection
