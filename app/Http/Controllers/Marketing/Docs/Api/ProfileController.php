<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing\Docs\Api;

use App\Http\Controllers\Controller;
use App\Jobs\RecordMarketingPageVisit;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        RecordMarketingPageVisit::dispatch(viewName: 'marketing.docs.api.account.profile');

        return view('marketing.docs.api.account.profile');
    }
}
