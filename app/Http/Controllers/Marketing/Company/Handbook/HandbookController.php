<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing\Company\Handbook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * This controller is used to handle the marketing handbook pages.
 * It should be one of the only controllers that does not follow the naming convention
 * for methods in a controller.
 */
final class HandbookController extends Controller
{
    public function index(Request $request): View
    {
        return view('marketing.company.handbook.index');
    }
}
