<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index()
    {
        return redirect()->route('user-index');
    }

    public function loginPage()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
    	return view('admin_login');
    }
}
