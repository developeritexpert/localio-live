@extends('admin_layout.master')
@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">Contributor Badges</h3>
                        </div>
                        <div class="nk-block-head-content">
                            <div class="toggle-wrap nk-block-tools-toggle">
                                <a href="{{ route('admin.contributor.badges.create') }}" class="btn btn-primary"><em class="icon ni ni-plus"></em><span>Add Badge</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="nk-block">
                    <div class="card card-bordered card-stretch">
                        <div class="card-inner-group">
                            <div class="card-inner p-0">
                                <table class="table table-orders">
                                    <thead class="tb-odr-head">
                                        <tr class="tb-odr-item">
                                            <th class="tb-odr-info">
                                                <span class="tb-odr-id">Name</span>
                                            </th>
                                            <th class="tb-odr-amount">
                                                <span class="tb-odr-total">Min Reviews</span>
                                            </th>
                                            <th class="tb-odr-amount">
                                                <span class="tb-odr-total">Max Reviews</span>
                                            </th>
                                            <th class="tb-odr-amount">
                                                <span class="tb-odr-total">Icon</span>
                                            </th>
                                            <th class="tb-odr-action">&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody class="tb-odr-body">
                                        @foreach($badges as $badge)
                                        <tr class="tb-odr-item">
                                            <td class="tb-odr-info">
                                                <span class="title">{{ $badge->name }}</span>
                                            </td>
                                            <td class="tb-odr-amount">
                                                <span class="amount">{{ $badge->min_reviews }}</span>
                                            </td>
                                            <td class="tb-odr-amount">
                                                <span class="amount">{{ $badge->max_reviews ?? '51+' }}</span>
                                            </td>
                                            <td class="tb-odr-amount">
                                                <span class="amount">{!! $badge->icon !!}</span>
                                            </td>
                                            <td class="tb-odr-action">
                                                <div class="dropdown">
                                                    <a class="text-soft dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown" data-offset="-8,0"><em class="icon ni ni-more-h"></em></a>
                                                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                                                        <ul class="link-list-plain">
                                                            <li><a href="{{ route('admin.contributor.badges.edit', $badge->id) }}" class="text-primary">Edit</a></li>
                                                            <li><a href="{{ route('admin.contributor.badges.delete', $badge->id) }}" class="text-danger" onclick="return confirm('Are you sure?')">Delete</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
