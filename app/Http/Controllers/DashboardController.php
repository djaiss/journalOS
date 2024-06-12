<?php

namespace App\Http\Controllers;

use App\Http\ViewModels\DashboardViewModel;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'data' => '',
        ]);
    }
}
