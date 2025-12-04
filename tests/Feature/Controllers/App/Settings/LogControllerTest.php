<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Settings;

use App\Models\Log;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class LogControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_all_the_logs(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));
        $user = User::factory()->create([
            'first_name' => 'Ross',
            'last_name' => 'Geller',
            'nickname' => null,
        ]);

        $log = Log::factory()->create([
            'journal_id' => null,
            'user_id' => $user->id,
            'action' => 'profile_update',
            'description' => 'Updated their profile',
        ]);

        $response = $this->actingAs($user)
            ->get('/settings/profile/logs');

        $response->assertStatus(200);
        $response->assertViewIs('app.settings.logs.index');
        $response->assertViewHas('logs');

        $logs = $response->viewData('logs');

        $this->assertCount(1, $logs);
        $this->assertEquals($log->id, $logs[0]->id);
        $this->assertEquals('profile_update', $logs[0]->action);
        $this->assertEquals('Updated their profile', $logs[0]->description);
    }

    #[Test]
    public function it_shows_a_pagination(): void
    {
        $user = User::factory()->create();

        Log::factory()->count(15)->create([
            'journal_id' => null,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->get('/settings/profile/logs');

        $response->assertStatus(200);
        $this->assertCount(10, $response['logs']);

        $this->assertTrue($response['logs']->hasMorePages());
    }
}
