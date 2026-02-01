<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Api\Settings\Account;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class PruneAccountControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_prunes_the_authenticated_users_account(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $otherJournal = Journal::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('PUT', '/api/settings/prune');

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'The account has been pruned',
            'status' => 200,
        ]);

        $this->assertDatabaseMissing('journals', [
            'id' => $journal->id,
        ]);

        $this->assertDatabaseHas('journals', [
            'id' => $otherJournal->id,
        ]);
    }
}
