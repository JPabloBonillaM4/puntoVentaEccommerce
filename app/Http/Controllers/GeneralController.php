<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function index()
    {
        return view('pages.login');
    }

    public function dashboard_index()
    {
        return view('pages.admin.index');
    }
}
