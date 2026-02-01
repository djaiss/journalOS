<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Marketing\Company\Handbook;

use App\Helpers\MarketingHelper;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

final class HandbookProjectController extends Controller
{
    public function index(): View
    {
        $stats = MarketingHelper::getStats('marketing.company.handbook.project');

        return view('marketing.company.handbook.project', [
            'stats' => $stats,
        ]);
    }
}
