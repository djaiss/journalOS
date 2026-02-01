<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App\Settings;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class PruneAccountControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/settings/account');

        $response->assertStatus(200);
        $response->assertSee('Prune your account');
    }

    #[Test]
    public function user_can_prune_their_account(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->from('/settings/account')
            ->put('/settings/prune');

        $response->assertRedirect('/settings/account');
        $response->assertSessionHas('status', 'The account has been pruned');
        $this->assertDatabaseMissing('journals', [
            'id' => $journal->id,
        ]);
    }

    #[Test]
    public function unauthenticated_user_cannot_prune_account(): void
    {
        $response = $this->put('/settings/prune');

        $response->assertRedirect('/login');
    }
}
