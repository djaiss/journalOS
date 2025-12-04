<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing\Docs\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AuthenticationController extends Controller
{
    public function index(Request $request): View
    {
        return view('marketing.docs.api.authentication');
    }
}
