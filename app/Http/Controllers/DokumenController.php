<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DokumenController extends Controller
{
    public function index()
    {
        return view('pages.dokumen.upload.list');
    }
    public function valid()
    {
        return view('pages.dokumen.validate.list');
    }
}
