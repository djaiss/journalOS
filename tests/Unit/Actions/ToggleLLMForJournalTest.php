<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\ToggleLLMForJournal;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ToggleLLMForJournalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_toggles_llm_visibility_and_sets_access_key(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'has_llm_access' => false,
            'llm_access_key' => null,
        ]);

        $updatedJournal = new ToggleLLMForJournal(
            user: $user,
            journal: $journal,
        )->execute();

        $this->assertTrue($updatedJournal->has_llm_access);
        $this->assertNotNull($updatedJournal->llm_access_key);
        $this->assertSame(64, mb_strlen($updatedJournal->llm_access_key));

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'has_llm_access' => true,
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user, $journal): bool {
                return (
                    $job->action === 'journal_llm_visibility_toggled'
                    && $job->user->is($user)
                    && $job->journal?->is($journal)
                );
            },
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: function (UpdateUserLastActivityDate $job) use ($user): bool {
                return $job->user->is($user);
            },
        );
    }

    #[Test]
    public function it_clears_access_key_when_disabling(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'has_llm_access' => true,
            'llm_access_key' => 'existing-key',
        ]);

        $updatedJournal = new ToggleLLMForJournal(
            user: $user,
            journal: $journal,
        )->execute();

        $this->assertFalse($updatedJournal->has_llm_access);
        $this->assertNull($updatedJournal->llm_access_key);

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'has_llm_access' => false,
            'llm_access_key' => null,
        ]);
    }

    #[Test]
    public function it_throws_when_visibility_state_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'has_llm_access' => true,
        ]);

        $journal->setRawAttributes(array_merge(
            $journal->getAttributes(),
            ['has_llm_access' => null],
        ));

        new ToggleLLMForJournal(
            user: $user,
            journal: $journal,
        )->execute();
    }

    #[Test]
    public function it_throws_when_journal_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'has_llm_access' => true,
        ]);

        new ToggleLLMForJournal(
            user: $user,
            journal: $journal,
        )->execute();
    }
}
