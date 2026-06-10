<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Session;
class UserController extends Controller
{
    //
    public function index()
    {
        return view('User.contact.index');
    }

}
