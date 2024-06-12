<?php

namespace App\Services;

use App\Models\Journal;

class CreateJournal extends BaseService
{
    private Journal $journal;

    public function __construct(
        public string $name,
    ) {
    }

    public function execute(): Journal
    {
        $this->create();

        return $this->journal;
    }

    private function create(): void
    {
        $this->journal = Journal::create([
            'user_id' => auth()->user()->id,
            'name' => $this->name,
        ]);
    }
}
