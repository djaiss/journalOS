<?php

declare(strict_types=1);
use App\Models\Organization;
use App\Models\User;

it('shows the organizations the user is a member of', function (): void {
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
});

it('shows a message when the user is not a member of any organizations', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/organizations');

    $response->assertStatus(200);
    $response->assertSee('You are not a member of any organizations yet.');
});

it('creates an organization', function (): void {
    $user = User::factory()->create([
        'email' => 'michael.scott@dundermifflin.com',
        'password' => Illuminate\Support\Facades\Hash::make('5UTHSmdj'),
    ]);

    $response = $this->actingAs($user)->get('/organizations/create');

    $response = $this->post('/organizations', [
        'organization_name' => 'Dunder Mifflin',
    ]);

    $organization = Organization::where('name', 'Dunder Mifflin')->first();

    $response->assertRedirect('/organizations/' . $organization->slug);
});

it('lets an user access an organization', function (): void {
    $user = User::factory()->create();
    $organization = Organization::factory()->create();
    $user->organizations()->attach($organization->id, [
        'joined_at' => now(),
    ]);

    $response = $this->actingAs($user)->get('/organizations/' . $organization->slug);

    $response->assertStatus(200);
});

it('does not let an user access an organization they are not a member of', function (): void {
    $user = User::factory()->create();
    $organization = Organization::factory()->create();

    $response = $this->actingAs($user)->get('/organizations/' . $organization->slug);

    $response->assertStatus(403);
});
