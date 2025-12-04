<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class JournalTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_a_user(): void
    {
        $henri = User::factory()->create();
        $journal = Journal::factory()->create(['user_id' => $henri->id]);

        $this->assertTrue($journal->user()->exists());
    }

    #[Test]
    public function it_gets_the_avatar(): void
    {
        $journal = Journal::factory()->create();

        $this->assertIsString($journal->avatar());
    }
}
