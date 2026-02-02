<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Marketing\Pricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PricingController extends Controller
{
    public function index(): View
    {
        return view('marketing.pricing.index');
    }
}
