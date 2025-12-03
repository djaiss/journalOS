<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Organizations;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class OrganizationControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_organizations_the_user_is_a_member_of(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create([
            'name' => 'Dunder Mifflin',
            'slug' => 'dunder-mifflin',
        ]);
        $user->organizations()->attach($organization->id, [
            'joined_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/organizations');

        $response->assertStatus(200);
        $response->assertSee('Dunder Mifflin');
    }

    #[Test]
    public function it_shows_a_message_when_the_user_is_not_a_member_of_any_organizations(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/organizations');

        $response->assertStatus(200);
        $response->assertSee('You are not a member of any organizations yet.');
    }

    #[Test]
    public function it_creates_an_organization(): void
    {
        $user = User::factory()->create([
            'email' => 'michael.scott@dundermifflin.com',
            'password' => Hash::make('5UTHSmdj'),
        ]);

        $response = $this->actingAs($user)->get('/organizations/create');

        $response = $this->post('/organizations', [
            'organization_name' => 'Dunder Mifflin',
        ]);

        $organization = Organization::where('name', 'Dunder Mifflin')->first();

        $response->assertRedirect('/organizations/' . $organization->slug);
    }

    #[Test]
    public function it_lets_an_user_access_an_organization(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $user->organizations()->attach($organization->id, [
            'joined_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/organizations/' . $organization->slug);

        $response->assertStatus(200);
    }

    #[Test]
    public function it_does_not_let_an_user_access_an_organization_they_are_not_a_member_of(): void
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $response = $this->actingAs($user)->get('/organizations/' . $organization->slug);

        $response->assertStatus(403);
    }
}
