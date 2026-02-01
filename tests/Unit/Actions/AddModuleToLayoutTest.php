<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\AddModuleToLayout;
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

final class AddModuleToLayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_adds_a_module_to_a_layout_and_shifts_positions(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
            'columns_count' => 3,
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

        $layoutModule = new AddModuleToLayout(
            user: $user,
            layout: $layout,
            moduleKey: 'mood',
            columnNumber: 1,
            requestedPosition: 2,
        )->execute();

        $this->assertEquals('mood', $layoutModule->module_key);
        $this->assertEquals(1, $layoutModule->column_number);
        $this->assertEquals(2, $layoutModule->position);
        $this->assertEquals(1, $moduleOne->fresh()->position);
        $this->assertEquals(3, $moduleTwo->fresh()->position);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job) => (
                $job->action === 'layout_module_add'
                && $job->user->id === $user->id
                && $job->journal?->id === $journal->id
                && str_contains($job->description, $journal->name)
            ),
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: fn (UpdateUserLastActivityDate $job) => $job->user->id === $user->id,
        );
    }

    #[Test]
    public function it_throws_an_exception_when_module_key_is_invalid(): void
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

        new AddModuleToLayout(
            user: $user,
            layout: $layout,
            moduleKey: 'invalid-module',
            columnNumber: 1,
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

        new AddModuleToLayout(
            user: $user,
            layout: $layout,
            moduleKey: 'sleep',
            columnNumber: 1,
        )->execute();
    }
}
