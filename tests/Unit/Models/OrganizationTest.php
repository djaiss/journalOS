<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\EmailSent;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class OrganizationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_many_users(): void
    {
        $organization = Organization::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $organization->users()->attach($user1->id, [
            'joined_at' => now(),
        ]);
        $organization->users()->attach($user2->id, [
            'joined_at' => now(),
        ]);

        $this->assertCount(2, $organization->users);
        $this->assertTrue($organization->users->contains($user1));
        $this->assertTrue($organization->users->contains($user2));
    }

    #[Test]
    public function it_has_many_emails_sent(): void
    {
        $organization = Organization::factory()->create();
        EmailSent::factory()->count(2)->create([
            'organization_id' => $organization->id,
        ]);

        $this->assertTrue($organization->emailsSent()->exists());
    }

    #[Test]
    public function it_gets_the_avatar(): void
    {
        $organization = Organization::factory()->create();

        $this->assertIsString($organization->getAvatar());
    }
}
