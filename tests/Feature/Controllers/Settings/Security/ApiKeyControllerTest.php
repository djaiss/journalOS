<?php

declare(strict_types=1);

use App\Models\User;

it('it can create a new api token', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->from('/settings/security/create')
        ->post('/settings/api-keys', [
            'label' => 'My API Token',
        ]);

    $response->assertRedirect('/settings/security');
    $response->assertSessionHas('status', 'API key created');

    $this->assertDatabaseHas('personal_access_tokens', [
        'name' => 'My API Token',
        'tokenable_id' => $user->id,
    ]);
});

it('it can delete an api token', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('Test API Token');

    $response = $this->actingAs($user)
        ->delete('/settings/api-keys/' . $token->accessToken->id);

    $response->assertRedirect('/settings/security');
    $response->assertSessionHas('status', 'API key deleted');

    $this->assertDatabaseMissing('personal_access_tokens', [
        'id' => $token->accessToken->id,
    ]);
});
