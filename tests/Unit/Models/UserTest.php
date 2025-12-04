<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\EmailSent;
use App\Models\Journal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_many_journals(): void
    {
        $user = User::factory()->create();
        Journal::factory()->count(2)->create([
            'user_id' => $user->id,
        ]);

        $this->assertCount(2, $user->journals);
    }

    #[Test]
    public function it_has_many_emails_sent(): void
    {
        $user = User::factory()->create();
        EmailSent::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($user->emailsSent()->exists());
    }

    #[Test]
    public function it_gets_the_name(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Dwight',
            'last_name' => 'Schrute',
            'nickname' => null,
        ]);

        $this->assertEquals('Dwight Schrute', $user->getFullName());

        $user->nickname = 'The Beet Farmer';
        $user->save();
        $this->assertEquals('The Beet Farmer', $user->getFullName());
    }

    #[Test]
    public function it_has_initials(): void
    {
        $dwight = User::factory()->create([
            'first_name' => 'Dwight',
            'last_name' => 'Schrute',
        ]);

        $this->assertEquals('DS', $dwight->initials());
    }
}
