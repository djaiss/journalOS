<?php

declare(strict_types=1);

namespace App\Http\ViewModels\Settings;

use App\Models\EmailSent;
use App\Models\Log;
use App\Models\User;
use Illuminate\Support\Collection;

final readonly class ProfileShowViewModel
{
    public function __construct(
        private User $user,
    ) {}

    public function hasMoreLogs(): bool
    {
        return Log::where('user_id', $this->user->id)->count() > 5;
    }

    public function hasMoreEmailsSent(): bool
    {
        return EmailSent::where('user_id', $this->user->id)->count() > 5;
    }

    public function logs(): Collection
    {
        return Log::where('user_id', $this->user->id)
            ->with('user')
            ->with('organization')
            ->take(5)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn(Log $log) => (object) [
                'username' => $log->getUserName(),
                'action' => $log->action,
                'organization_name' => $log->organization?->name,
                'organization_id' => $log->organization?->id,
                'description' => $log->description,
                'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                'created_at_diff_for_humans' => $log->created_at->diffForHumans(),
            ]);
    }

    public function emailsSent(): Collection
    {
        return EmailSent::where('user_id', $this->user->id)
            ->take(5)
            ->orderBy('sent_at', 'desc')
            ->get()
            ->map(fn(EmailSent $emailSent) => (object) [
                'email_type' => $emailSent->email_type,
                'email_address' => $emailSent->email_address,
                'subject' => $emailSent->subject,
                'body' => $emailSent->body,
                'sent_at' => $emailSent->sent_at?->diffForHumans(),
                'delivered_at' => $emailSent->delivered_at?->diffForHumans(),
                'bounced_at' => $emailSent->bounced_at?->diffForHumans(),
            ]);
    }
}
