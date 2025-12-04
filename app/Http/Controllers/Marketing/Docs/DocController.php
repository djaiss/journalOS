<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing\Docs;

use App\Http\Controllers\Controller;
use App\Jobs\RecordMarketingPageVisit;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DocController extends Controller
{
    public function index(Request $request): View
    {
        RecordMarketingPageVisit::dispatch(viewName: 'marketing.docs.api.introduction')->onQueue('low');

        return view('marketing.docs.api.introduction');
    }
}
