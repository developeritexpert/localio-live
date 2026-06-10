<?php

namespace App\Http\Controllers;

use App\Models\ExclusiveDeal;
use Illuminate\Http\Request;

class AdminDealController extends Controller
{
    public function index(){    
        return view('Admin.deal.index');
    }
    public function create(){
        return view('Admin.deal.create');
    }
    public function edit($id)
    {
        return view('Admin.deal.edit',['dealId' => $id]);
    }
}
