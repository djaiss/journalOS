<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing\Docs\Api\Modules;

use App\Http\Controllers\Controller;
use App\Jobs\RecordMarketingPageVisit;
use Illuminate\View\View;

final class MealController extends Controller
{
    public function index(): View
    {
        RecordMarketingPageVisit::dispatch(viewName: 'marketing.docs.api.modules.meal')->onQueue('low');

        return view('marketing.docs.api.modules.meal');
    }
}
