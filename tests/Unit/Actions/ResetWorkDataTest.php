<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\ResetWorkData;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ResetWorkDataTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_resets_work_data_for_a_journal_entry(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'worked' => true,
            'work_mode' => 'focused',
            'work_load' => 'heavy',
            'work_procrastinated' => false,
        ]);

        $result = (new ResetWorkData(
            user: $user,
            entry: $entry,
        ))->execute();

        $this->assertNull($result->worked);
        $this->assertNull($result->work_mode);
        $this->assertNull($result->work_load);
        $this->assertNull($result->work_procrastinated);

        $this->assertDatabaseHas('journal_entries', [
            'id' => $entry->id,
            'worked' => null,
            'work_mode' => null,
            'work_load' => null,
            'work_procrastinated' => null,
        ]);

        $this->assertInstanceOf(JournalEntry::class, $result);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'work_data_reset' && $job->user->id === $user->id;
            },
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: function (UpdateUserLastActivityDate $job) use ($user): bool {
                return $job->user->id === $user->id;
            },
        );
    }

    #[Test]
    public function it_throws_when_entry_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Journal not found');

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $journal = Journal::factory()->for($otherUser)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        (new ResetWorkData(
            user: $user,
            entry: $entry,
        ))->execute();
    }
}
