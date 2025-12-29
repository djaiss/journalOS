<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing\Docs\Api;

use App\Http\Controllers\Controller;
use App\Jobs\RecordMarketingPageVisit;
use Illuminate\View\View;

final class ApiIntroductionController extends Controller
{
    public function index(): View
    {
        RecordMarketingPageVisit::dispatch(viewName: 'marketing.docs.api.introduction')->onQueue('low');

        return view('marketing.docs.api.introduction');
    }
}
