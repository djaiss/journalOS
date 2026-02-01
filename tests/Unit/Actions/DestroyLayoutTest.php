<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\DestroyLayout;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Layout;
use App\Models\LayoutModule;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class DestroyLayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_deletes_the_layout_and_clears_related_entries(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $layoutModule = LayoutModule::factory()->create([
            'layout_id' => $layout->id,
            'module_key' => 'sleep',
            'column_number' => 1,
            'position' => 1,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'layout_id' => $layout->id,
        ]);

        new DestroyLayout(
            user: $user,
            layout: $layout,
        )->execute();

        $this->assertDatabaseMissing('layouts', [
            'id' => $layout->id,
        ]);
        $this->assertDatabaseMissing('layout_modules', [
            'id' => $layoutModule->id,
        ]);
        $this->assertDatabaseHas('journal_entries', [
            'id' => $entry->id,
            'layout_id' => null,
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job) => (
                    $job->action === 'layout_destroy'
                    && $job->user->id === $user->id
                    && $job->journal?->id === $journal->id
                ),
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: fn (UpdateUserLastActivityDate $job) => $job->user->id === $user->id,
        );
    }

    #[Test]
    public function it_throws_when_layout_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $layout = Layout::factory()->create();

        new DestroyLayout(
            user: $user,
            layout: $layout,
        )->execute();
    }
}
