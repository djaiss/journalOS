<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Settings;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmailResource;
use App\Models\EmailSent;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

final class EmailSentController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $emails = EmailSent::query()
            ->where('user_id', Auth::user()->id)
            ->latest('sent_at')
            ->paginate(10);

        return EmailResource::collection($emails);
    }

    public function show(int $id): EmailResource
    {
        $email = EmailSent::query()->find($id);

        if (!$email || $email->user_id === null) {
            abort(404, 'Email not found.');
        }

        if ($email->user_id !== Auth::user()->id) {
            abort(403, 'Unauthorized action.');
        }

        return new EmailResource($email);
    }
}
