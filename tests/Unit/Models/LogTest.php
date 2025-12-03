<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Log;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LogTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_an_organization(): void
    {
        $log = Log::factory()->create();

        $this->assertTrue($log->organization()->exists());
    }

    #[Test]
    public function it_belongs_to_a_user(): void
    {
        $log = Log::factory()->create();

        $this->assertTrue($log->user()->exists());
    }

    #[Test]
    public function it_gets_the_name_of_the_user(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Dwight',
            'last_name' => 'Schrute',
            'nickname' => null,
        ]);
        $log = Log::factory()->create([
            'user_id' => $user->id,
            'user_name' => 'Jim Halpert',
        ]);

        $this->assertEquals('Dwight Schrute', $log->getUserName());

        $log->user_id = null;
        $log->save();

        $this->assertEquals('Jim Halpert', $log->refresh()->getUserName());
    }
}
