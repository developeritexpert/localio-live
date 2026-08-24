<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ContributorBadgeController extends Controller
{
    public function index()
    {
        return view('admin.contributor-badges.index');
    }
}