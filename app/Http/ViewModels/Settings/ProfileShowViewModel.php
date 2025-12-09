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
        return Log::query()->where('user_id', $this->user->id)->count() > 5;
    }

    public function hasMoreEmailsSent(): bool
    {
        return EmailSent::query()->where('user_id', $this->user->id)->count() > 5;
    }

    public function logs(): Collection
    {
        return Log::query()->where('user_id', $this->user->id)
            ->with('user')
            ->with('journal')
            ->take(5)->latest()
            ->get()
            ->map(fn(Log $log) => (object) [
                'action' => $log->action,
                'journal_name' => $log->journal?->name,
                'journal_id' => $log->journal?->id,
                'description' => $log->description,
                'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                'created_at_diff_for_humans' => $log->created_at->diffForHumans(),
            ]);
    }

    public function emailsSent(): Collection
    {
        return EmailSent::query()->where('user_id', $this->user->id)
            ->take(5)
            ->latest('sent_at')
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
