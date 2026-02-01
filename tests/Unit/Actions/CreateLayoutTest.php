<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\CreateLayout;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\Layout;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CreateLayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_creates_a_layout(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $layout = new CreateLayout(
            user: $user,
            journal: $journal,
            name: 'Daily Review',
            columnsCount: 3,
        )->execute();

        $this->assertInstanceOf(Layout::class, $layout);
        $this->assertEquals($journal->id, $layout->journal_id);
        $this->assertEquals('Daily Review', $layout->name);
        $this->assertEquals(3, $layout->columns_count);
        $this->assertFalse($layout->is_active);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user, $journal): bool {
                return (
                    $job->action === 'layout_creation'
                    && $job->user->id === $user->id
                    && $job->journal?->id === $journal->id
                    && str_contains($job->description, $journal->name)
                );
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
    public function it_throws_an_exception_if_name_contains_special_characters(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        new CreateLayout(
            user: $user,
            journal: $journal,
            name: 'Daily@Review',
            columnsCount: 2,
        )->execute();
    }

    #[Test]
    public function it_throws_an_exception_if_columns_count_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        new CreateLayout(
            user: $user,
            journal: $journal,
            name: 'Daily Review',
            columnsCount: 5,
        )->execute();
    }

    #[Test]
    public function it_throws_an_exception_if_journal_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create();

        new CreateLayout(
            user: $user,
            journal: $journal,
            name: 'Daily Review',
            columnsCount: 2,
        )->execute();
    }
}
