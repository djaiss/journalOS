<?php

namespace App\Services;

use App\Models\Journal;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateJournal extends BaseService
{
    public function __construct(
        public Journal $journal,
        public string $name,
    ) {
    }

    public function execute(): Journal
    {
        $this->validate();
        $this->update();

        return $this->journal;
    }

    private function validate(): void
    {
        if ($this->journal->user_id !== auth()->user()->id) {
            throw new ModelNotFoundException;
        }
    }

    private function update(): void
    {
        $this->journal->name = $this->name;
        $this->journal->save();
    }
}
