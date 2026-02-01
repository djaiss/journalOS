<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Marketing\Docs\Api;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

final class ApiManagementController extends Controller
{
    public function index(): View
    {
        return view('marketing.docs.api.account.api-management');
    }
}
