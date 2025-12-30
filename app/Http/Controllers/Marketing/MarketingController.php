<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Jobs\RecordMarketingPageVisit;
use Illuminate\View\View;

final class MarketingController extends Controller
{
    public function index(): View
    {
        RecordMarketingPageVisit::dispatch(viewName: 'marketing.index')->onQueue('low');

        return view('marketing.index');
    }
}
