<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\RemoveModuleFromLayout;
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

final class RemoveModuleFromLayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_removes_a_module_and_shifts_positions(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
            'columns_count' => 2,
        ]);
        $moduleOne = LayoutModule::factory()->create([
            'layout_id' => $layout->id,
            'module_key' => 'sleep',
            'column_number' => 1,
            'position' => 1,
        ]);
        $moduleTwo = LayoutModule::factory()->create([
            'layout_id' => $layout->id,
            'module_key' => 'work',
            'column_number' => 1,
            'position' => 2,
        ]);
        $moduleThree = LayoutModule::factory()->create([
            'layout_id' => $layout->id,
            'module_key' => 'mood',
            'column_number' => 1,
            'position' => 3,
        ]);

        new RemoveModuleFromLayout(
            user: $user,
            layout: $layout,
            moduleKey: 'work',
        )->execute();

        $this->assertDatabaseMissing('layout_modules', [
            'id' => $moduleTwo->id,
        ]);
        $this->assertEquals(1, $moduleOne->fresh()->position);
        $this->assertEquals(2, $moduleThree->fresh()->position);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user, $journal): bool {
                return (
                    $job->action === 'layout_module_remove'
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
    public function it_throws_an_exception_when_module_is_missing(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
            'columns_count' => 2,
        ]);

        new RemoveModuleFromLayout(
            user: $user,
            layout: $layout,
            moduleKey: 'sleep',
        )->execute();
    }

    #[Test]
    public function it_throws_an_exception_when_layout_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $layout = Layout::factory()->create([
            'columns_count' => 2,
        ]);

        new RemoveModuleFromLayout(
            user: $user,
            layout: $layout,
            moduleKey: 'sleep',
        )->execute();
    }
}
