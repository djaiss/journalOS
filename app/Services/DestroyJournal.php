<?php

namespace App\Services;

use App\Models\Journal;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DestroyJournal extends BaseService
{
    public function __construct(
        public Journal $journal,
    ) {
    }

    public function execute(): void
    {
        $this->validate();
        $this->delete();
    }

    private function validate(): void
    {
        if ($this->journal->user_id !== auth()->user()->id) {
            throw new ModelNotFoundException;
        }
    }

    private function delete(): void
    {
        $this->journal->delete();
    }
}
