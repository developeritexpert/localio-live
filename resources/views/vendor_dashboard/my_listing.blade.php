@extends('vendor_dashboard_layout.master')
@section('content')
<div class="col-lg-9 p-0">
    <div class="user_content">
        <div class="heading-bar">
            <h1>My Listings</h1>
            <div class="btnn">
                <a href="javascript:void(0)" class="btn blue-btn">Add New Listing</a>
            </div>
        </div>
        <div class="listing-table">
            <table class="pricing-table">
                <tbody>
                    <tr>
                        <td>
                            <div class="user-info">
                                <div class="user-name">
                                    <img src="{{asset('vender_dashboard/img/lst1.png')}}" alt="Odoo" class="logo">
                                </div>
                                <div class="user-data">
                                    <h3>Odoo</h3>
                                    <div class="rating">
                                        <div class="star-box">
                                            <span>5.0</span>
                                            <div class="star">
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                            </div>
                                            <div class="down-arrow">
                                                <i class="fa-solid fa-chevron-down"></i>
                                            </div>
                                        </div>
                                        <p>124 ratings</p>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td><span class="price">$12</span> / Month</td>
                        <td><a href="#" class="btn blue-btn">View Details <img src="{{asset('vender_dashboard/img/arrowdown.png')}}"
                                    alt=""></a></td>
                        <td class="edit-icon"><a href="javascript:void(0)" class="btn blue-btn edit"><i
                                    class="fa-solid fa-pencil"></i></a></td>
                        <td class="menu-icon"><a href="javascript:void(0)"><i
                                    class="fa-solid fa-ellipsis-vertical"></i></a></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="user-info">
                                <div class="user-name">
                                    <img src="{{asset('vender_dashboard/img/lst2.png')}}" alt="BambooHR" class="logo">
                                </div>
                                <div class="user-data">
                                    <h3>BambooHR</h3>
                                    <div class="rating">
                                        <div class="star-box">
                                            <span>5.0</span>
                                            <div class="star">
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                            </div>
                                            <div class="down-arrow">
                                                <i class="fa-solid fa-chevron-down"></i>
                                            </div>
                                        </div>
                                        <p>124 ratings</p>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td><span class="price">$10</span> / Month</td>
                        <td><a href="#" class="btn blue-btn">View Details <img src="{{asset('vender_dashboard/img/arrowdown.png')}}"
                                    alt=""></a></td>
                        <td class="edit-icon"><a href="javascript:void(0)" class="btn blue-btn edit"><i
                                    class="fa-solid fa-pencil"></i></a></td>
                        <td class="menu-icon"><a href="javascript:void(0)"><i
                                    class="fa-solid fa-ellipsis-vertical"></i></a></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="user-info">
                                <div class="user-name">
                                    <img src="{{asset('vender_dashboard/img/lst3.png')}}" alt="Asana" class="logo">
                                </div>
                                <div class="user-data">
                                    <h3>Asana</h3>
                                    <div class="rating">
                                        <div class="star-box">
                                            <span>5.0</span>
                                            <div class="star">
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                            </div>
                                            <div class="down-arrow">
                                                <i class="fa-solid fa-chevron-down"></i>
                                            </div>
                                        </div>
                                        <p>124 ratings</p>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td><span class="price">$15</span> / Month</td>
                        <td><a href="#" class="btn blue-btn">View Details <img src="{{asset('vender_dashboard/img/arrowdown.png')}}"
                                    alt=""></a></td>
                        <td class="edit-icon"><a href="javascript:void(0)" class="btn blue-btn edit"><i
                                    class="fa-solid fa-pencil"></i></a></td>
                        <td class="menu-icon"><a href="javascript:void(0)"><i
                                    class="fa-solid fa-ellipsis-vertical"></i></a></td>
                    </tr>
                    <tr>
                        <td>
                            <div class="user-info">
                                <div class="user-name">
                                    <img src="{{asset('vender_dashboard/img/lst4.png')}}" alt="Confluence" class="logo">
                                </div>
                                <div class="user-data">
                                    <h3>Confluence</h3>
                                    <div class="rating">
                                        <div class="star-box">
                                            <span>5.0</span>
                                            <div class="star">
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                                <i class="fa-solid fa-star"></i>
                                            </div>
                                            <div class="down-arrow">
                                                <i class="fa-solid fa-chevron-down"></i>
                                            </div>
                                        </div>
                                        <p>124 ratings</p>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td><span class="price">$15</span> / Month</td>
                        <td><a href="#" class="btn blue-btn">View Details <img src="{{asset('vender_dashboard/img/arrowdown.png')}}"
                                    alt=""></a></td>
                        <td class="edit-icon"><a href="javascript:void(0)" class="btn blue-btn edit"><i
                                    class="fa-solid fa-pencil"></i></a></td>
                        <td class="menu-icon"><a href="javascript:void(0)"><i
                                    class="fa-solid fa-ellipsis-vertical"></i></a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
