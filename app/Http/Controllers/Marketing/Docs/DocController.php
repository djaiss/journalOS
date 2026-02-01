<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing\Docs;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

final class DocController extends Controller
{
    public function index(): RedirectResponse
    {
        return to_route('marketing.docs.concepts.modules');
    }
}
