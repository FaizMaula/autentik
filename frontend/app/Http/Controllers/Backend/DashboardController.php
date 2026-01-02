<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show a simple backend dashboard page.
     */
    public function index(Request $request)
    {
        return view('backend.dashboard');
    }
}
