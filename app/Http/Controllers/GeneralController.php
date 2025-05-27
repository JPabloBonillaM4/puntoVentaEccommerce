<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function index()
    {
        return view('pages.landing_page.index');
    }

    public function login() {
        return view('pages.login');
    }

    public function dashboard_index()
    {
        return redirect()->route('about.index');
        // return view('pages.admin.index');
    }

    public function about()
    {
        return view('pages.acerca_nosotros.index');
    }
}
