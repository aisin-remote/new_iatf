<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function dashboard_rule() {
        return view('pages-rule.dashboard-guest');
    }
}
