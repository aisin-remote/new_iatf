<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DepartemenController extends Controller
{
    public function dashboard()
    {
        return view('pages.dashboard-user');
    }
}
