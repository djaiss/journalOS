<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Marketing\Docs\Modules;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ModulesController extends Controller
{
    public function index(): View
    {
        return view('marketing.docs.modules.index');
    }
}
