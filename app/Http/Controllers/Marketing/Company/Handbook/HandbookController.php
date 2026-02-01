<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Marketing\Company\Handbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class HandbookController extends Controller
{
    public function index(Request $request): View
    {
        return view('marketing.company.handbook.index');
    }
}
