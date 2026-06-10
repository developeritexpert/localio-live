<div>
  
    <?php
    $locale = getCurrentLocale();
    ?>
    <div class="nk-block nk-block-lg">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">Exclusive Deals</h3>
                </div>
                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em
                                class="icon ni ni-more-v"></em></a>
                        <div class="toggle-expand-content" data-content="pageMenu">
                            <ul class="nk-block-tools g-3">
                                <li class="nk-block-tools-opt">
                                    <a href="{{ route('exclusive-deals.create') }}"
                                        class=" btn btn-icon btn-primary d-md-none "><em class="icon ni ni-plus"></em></a>
                                    <a href="{{ route('exclusive-deals.create') }}"
                                        class=" btn btn-primary d-none d-md-inline-flex btn-localio"><em
                                            class=""></em><span>Add Deal</span></a>
                                </li>
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
                            <th class="nk-tb-col"><span class="sub-text">Applies To</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Name</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Discount</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Price Type</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Country</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Period</span></th>
                            <th class="nk-tb-col"><span class="sub-text">Status</span></th>
                            <th class="nk-tb-col tb-tnx-action">
                                <span>Action</span>
                            </th>
                        </tr>
                    </thead>
                    @if (isset($deals) && $deals->isNotEmpty())
                        <tbody>
                            @foreach ($deals as $deal)
                            @php
                            $appliedModel = $deal->appliesTo;
                            $type = ucfirst($deal->applies_to_type);
                            $name = $appliedModel->translations->name ?? '-';
                            $category = $deal->applies_to_type === 'product' ? $appliedModel->category->name ?? '-' : ($deal->applies_to_type === 'category' ? $appliedModel->name ?? '-' : '-');
                        @endphp
                                <tr class="nk-tb-item">
                                    <td class="nk-tb-col">
                                        <div class="user-card">
                                            <div class="user-info">
                                                <span class="tb-lead">
                                                    {{$type}}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="nk-tb-col">
                                        <div class="user-card">
                                            <div class="user-info">
                                                <span class="tb-lead">
                                                   {{$name}}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="nk-tb-col tb-col-mb">
                                        <span class="tb-lead">
                                            {{ number_format($deal->discount_percent, 2) }}%
                                        </span>
                                    </td>
                                    <td class="nk-tb-col tb-col-mb">
                                        <span class="tb-lead">
                                            {{ ucfirst(str_replace('_', ' ', $deal->price_type)) }}
                                        </span>
                                    </td>
                                    <td class="nk-tb-col tb-col-mb">
                                        <span class="tb-lead">
                                            {{ $deal->country_code ?: 'Global' }}
                                        </span>
                                    </td>
                                    <td class="nk-tb-col tb-col-mb">
                                        <span class="tb-lead">
                                            {{ \Carbon\Carbon::parse($deal->starts_at)->format('M d, Y') }} -
                                            {{ \Carbon\Carbon::parse($deal->ends_at)->format('M d, Y') }}
                                        </span>
                                    </td>
                                    <td class="nk-tb-col tb-col-mb">
                                        @if ($deal->status === 'active')
                                            <span class="tb-lead text-success">Active</span>
                                        @else
                                            <span class="tb-lead text-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="nk-tb-col nk-tb-col-tools">
                                        <div class="drodown">
                                            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger"
                                                data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                            <div class="dropdown-menu dropdown-menu-end" style="list-style: none; padding: 0; margin: 0; height:auto">
                                                <ul class="link-list-opt no-bdr">
                                                    <li>
                                                        <a href="{{ route('exclusive-deals.edit', ['id' => $deal->id]) }}">
                                                            <em class="icon ni ni-edit-fill"></em>
                                                            <span>Edit</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#" wire:click.prevent="deleteDeal({{ $deal->id }})">
                                                            <em class="icon ni ni-trash"></em>
                                                            <span>Delete</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
