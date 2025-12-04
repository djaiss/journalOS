<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Journal;
use App\Models\Log;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class LogTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_a_user(): void
    {
        $log = Log::factory()->create();

        $this->assertTrue($log->user()->exists());
    }

    #[Test]
    public function it_belongs_to_a_journal(): void
    {
        $journal = Journal::factory()->create();
        $log = Log::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $this->assertTrue($log->journal()->exists());
    }

    #[Test]
    public function it_gets_the_name_of_the_journal(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Dwight',
            'last_name' => 'Schrute',
            'nickname' => null,
        ]);
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'name' => 'Journal of Dwight',
        ]);
        $log = Log::factory()->create([
            'user_id' => $user->id,
            'journal_id' => $journal->id,
            'journal_name' => 'Jim Halpert',
        ]);

        $this->assertEquals('Journal of Dwight', $log->getJournalName());

        $log->journal_id = null;
        $log->save();

        $this->assertEquals('Jim Halpert', $log->refresh()->getJournalName());
    }
}
