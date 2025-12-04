<?php

declare(strict_types=1);

namespace App\Http\ViewModels\Journal;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Support\Collection;

final readonly class JournalIndexViewModel
{
    public function __construct(
        private User $user,
    ) {}

    public function journals(): Collection
    {
        return $this->user->journals()
            ->get()
            ->map(fn(Journal $journal) => (object) [
                'id' => $journal->id,
                'name' => $journal->name,
                'slug' => $journal->slug,
                'avatar' => $journal->avatar(),
            ]);
    }
}
