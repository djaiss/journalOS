<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing\Company\Handbook;

use App\Helpers\MarketingHelper;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

final class HandbookWritingController extends Controller
{
    public function index(): View
    {
        $stats = MarketingHelper::getStats('marketing.company.handbook.writing');

        return view('marketing.company.handbook.writing', [
            'stats' => $stats,
        ]);
    }
}
