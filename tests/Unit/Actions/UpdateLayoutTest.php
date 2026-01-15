<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\UpdateLayout;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\Layout;
use App\Models\LayoutModule;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class UpdateLayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_updates_a_layout(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
            'name' => 'Old Layout',
            'columns_count' => 2,
        ]);

        $updatedLayout = (new UpdateLayout(
            user: $user,
            layout: $layout,
            name: 'Updated Layout',
            columnsCount: 4,
        ))->execute();

        $this->assertEquals('Updated Layout', $updatedLayout->name);
        $this->assertEquals(4, $updatedLayout->columns_count);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user, $journal): bool {
                return $job->action === 'layout_update'
                    && $job->user->id === $user->id
                    && $job->journal?->id === $journal->id
                    && str_contains($job->description, $journal->name);
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
    public function it_removes_modules_in_deleted_columns_when_updating_layout(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
            'columns_count' => 3,
        ]);
        $keptModule = LayoutModule::factory()->create([
            'layout_id' => $layout->id,
            'module_key' => 'sleep',
            'column_number' => 2,
            'position' => 1,
        ]);
        $removedModule = LayoutModule::factory()->create([
            'layout_id' => $layout->id,
            'module_key' => 'work',
            'column_number' => 3,
            'position' => 1,
        ]);

        (new UpdateLayout(
            user: $user,
            layout: $layout,
            name: 'Updated Layout',
            columnsCount: 2,
        ))->execute();

        $this->assertDatabaseHas('layout_modules', [
            'id' => $keptModule->id,
        ]);
        $this->assertDatabaseMissing('layout_modules', [
            'id' => $removedModule->id,
        ]);
    }

    #[Test]
    public function it_throws_an_exception_if_name_contains_special_characters(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
        ]);

        (new UpdateLayout(
            user: $user,
            layout: $layout,
            name: 'Invalid@Layout',
            columnsCount: 2,
        ))->execute();
    }

    #[Test]
    public function it_throws_an_exception_if_columns_count_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
        ]);

        (new UpdateLayout(
            user: $user,
            layout: $layout,
            name: 'Valid Layout',
            columnsCount: 0,
        ))->execute();
    }

    #[Test]
    public function it_throws_an_exception_if_layout_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherJournal = Journal::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $layout = Layout::factory()->create([
            'journal_id' => $otherJournal->id,
        ]);

        (new UpdateLayout(
            user: $user,
            layout: $layout,
            name: 'Updated Layout',
            columnsCount: 3,
        ))->execute();
    }
}
