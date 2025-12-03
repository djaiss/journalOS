<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\EmailSent;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_many_organizations(): void
    {
        $user = User::factory()->create();
        $organization1 = Organization::factory()->create();
        $organization2 = Organization::factory()->create();

        $user->organizations()->attach($organization1->id, [
            'joined_at' => now(),
        ]);
        $user->organizations()->attach($organization2->id, [
            'joined_at' => now(),
        ]);

        $this->assertCount(2, $user->organizations);
        $this->assertTrue($user->organizations->contains($organization1));
        $this->assertTrue($user->organizations->contains($organization2));
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

    #[Test]
    public function it_checks_organization_membership(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $this->assertFalse($user->isPartOfOrganization($organization));

        $user->organizations()->attach($organization->id, [
            'joined_at' => now(),
        ]);
        $this->assertTrue($user->isPartOfOrganization($organization));
    }
}
