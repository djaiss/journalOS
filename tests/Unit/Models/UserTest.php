<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\EmailSent;
use App\Models\Journal;
use App\Models\User;
use App\Models\Log;
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
    public function it_has_many_logs(): void
    {
        $dwight = User::factory()->create();
        Log::factory()->create([
            'user_id' => $dwight->id,
        ]);

        $this->assertTrue($dwight->logs()->exists());
    }

    #[Test]
    public function it_has_many_books(): void
    {
        $user = User::factory()->create();
        Book::factory()->count(2)->create([
            'user_id' => $user->id,
        ]);

        $this->assertCount(2, $user->books);
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

    #[Test]
    public function it_checks_if_the_account_is_in_trial(): void
    {
        config(['services.journalos.enable_paid_version' => true]);
        $user = User::factory()->create([
            'has_lifetime_access' => false,
            'trial_ends_at' => now()->addDays(30),
        ]);
        $this->assertTrue($user->isInTrial());

        $user->trial_ends_at = now()->subDays(1);
        $user->save();
        $this->assertFalse($user->isInTrial());
    }

    #[Test]
    public function it_checks_if_the_account_needs_to_pay(): void
    {
        config(['services.journalos.enable_paid_version' => true]);
        $user = User::factory()->create([
            'has_lifetime_access' => false,
            'trial_ends_at' => now()->subMinutes(1),
        ]);
        $this->assertTrue($user->needsToPay());

        $user = User::factory()->create([
            'has_lifetime_access' => false,
            'trial_ends_at' => now()->addMinutes(1),
        ]);
        $this->assertFalse($user->needsToPay());
        $user = User::factory()->create([
            'has_lifetime_access' => true,
        ]);
        $this->assertFalse($user->needsToPay());

        config(['services.journalos.enable_paid_version' => false]);
        $user = User::factory()->create([
            'has_lifetime_access' => false,
            'trial_ends_at' => now()->subDays(31),
        ]);
        $this->assertFalse($user->needsToPay());
        $user = User::factory()->create([
            'has_lifetime_access' => false,
            'trial_ends_at' => now()->subDays(29),
        ]);
        $this->assertFalse($user->needsToPay());
    }
}
