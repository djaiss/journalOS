<?php

declare(strict_types=1);

namespace Tests\Unit\Helpers;

use App\Helpers\TimeHelper;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class TimeHelperTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_formats_time_in_24h_format(): void
    {
        $user = User::factory()->create([
            'time_format_24h' => true,
        ]);
        Auth::shouldReceive('user')->andReturn($user);

        $result = TimeHelper::format('14:30');
        $this->assertEquals('14:30', $result);
    }

    #[Test]
    public function it_formats_time_in_12h_format(): void
    {
        $user = User::factory()->create([
            'time_format_24h' => false,
        ]);
        Auth::shouldReceive('user')->andReturn($user);

        $result = TimeHelper::format('14:30');
        $this->assertEquals('02:30 PM', $result);
    }
}
