<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Marketing\Features;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class FeaturesController extends Controller
{
    public function index(): View
    {
        return view('marketing.features.modules');
    }
}
