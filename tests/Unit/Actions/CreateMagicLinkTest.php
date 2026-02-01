<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\CreateMagicLink;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CreateMagicLinkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_returns_a_string(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $magicLinkUrl = new CreateMagicLink(
            email: $user->email,
        )->execute();

        $this->assertIsString($magicLinkUrl);

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: function (UpdateUserLastActivityDate $job) use ($user): bool {
                return $job->user->id === $user->id;
            },
        );
    }

    #[Test]
    public function it_contains_the_app_url_with_magic_link_structure(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $magicLinkUrl = new CreateMagicLink(
            email: $user->email,
        )->execute();

        $appUrl = config('app.url');
        $this->assertStringStartsWith($appUrl . '/magiclink/', $magicLinkUrl);
        $this->assertMatchesRegularExpression('/\/magiclink\/[a-f0-9-]+%3A[A-Za-z0-9]+/', $magicLinkUrl);
    }

    #[Test]
    public function it_throws_an_exception_if_user_not_found(): void
    {
        $nonExistentEmail = 'nonexistent@example.com';

        $this->expectException(ModelNotFoundException::class);

        new CreateMagicLink(
            email: $nonExistentEmail,
        )->execute();

        Queue::assertNothingPushed();
    }
}
