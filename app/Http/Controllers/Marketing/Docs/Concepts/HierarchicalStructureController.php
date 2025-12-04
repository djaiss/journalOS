<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing\Docs\Concepts;

use App\Http\Controllers\Controller;
use App\Jobs\RecordMarketingPageVisit;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class HierarchicalStructureController extends Controller
{
    public function index(Request $request): View
    {
        RecordMarketingPageVisit::dispatch(viewName: 'marketing.docs.concepts.hierarchy-structure')->onQueue('low');

        return view('marketing.docs.concepts.hierarchy-structure');
    }
}
