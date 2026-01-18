<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing\Company\Handbook;

use App\Helpers\MarketingHelper;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

final class HandbookShippingController extends Controller
{
    public function index(): View
    {
        $stats = MarketingHelper::getStats('marketing.company.handbook.shipping');

        return view('marketing.company.handbook.shipping', [
            'stats' => $stats,
        ]);
    }
}
