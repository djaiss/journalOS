<?php

declare(strict_types=1);

namespace Tests\Unit\Helpers\Modules;

use App\Helpers\Modules\SleepHelper;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class SleepHelperTest extends TestCase
{
    #[Test]
    public function it_generates_range_of_times_without_selection(): void
    {
        $result = SleepHelper::range('22:00', null);

        $this->assertCount(5, $result);
        $this->assertEquals([
            'time' => '22:00',
            'is_selected' => false,
        ], $result[0]);
        $this->assertEquals([
            'time' => '23:00',
            'is_selected' => false,
        ], $result[1]);
        $this->assertEquals([
            'time' => '00:00',
            'is_selected' => false,
        ], $result[2]);
        $this->assertEquals([
            'time' => '01:00',
            'is_selected' => false,
        ], $result[3]);
        $this->assertEquals([
            'time' => '02:00',
            'is_selected' => false,
        ], $result[4]);
    }

    #[Test]
    public function it_generates_range_of_times_with_selection(): void
    {
        $result = SleepHelper::range('22:00', '23:00');

        $this->assertCount(5, $result);
        $this->assertEquals([
            'time' => '22:00',
            'is_selected' => false,
        ], $result[0]);
        $this->assertEquals([
            'time' => '23:00',
            'is_selected' => true,
        ], $result[1]);
        $this->assertEquals([
            'time' => '00:00',
            'is_selected' => false,
        ], $result[2]);
    }

    #[Test]
    public function it_generates_range_starting_at_midnight(): void
    {
        $result = SleepHelper::range('00:00', '02:00');

        $this->assertCount(5, $result);
        $this->assertEquals([
            'time' => '00:00',
            'is_selected' => false,
        ], $result[0]);
        $this->assertEquals([
            'time' => '01:00',
            'is_selected' => false,
        ], $result[1]);
        $this->assertEquals([
            'time' => '02:00',
            'is_selected' => true,
        ], $result[2]);
        $this->assertEquals([
            'time' => '03:00',
            'is_selected' => false,
        ], $result[3]);
        $this->assertEquals([
            'time' => '04:00',
            'is_selected' => false,
        ], $result[4]);
    }

    #[Test]
    public function it_shifts_time_forward_by_hours(): void
    {
        $result = SleepHelper::shift('22:00', 2);

        $this->assertEquals('00:00', $result);
    }

    #[Test]
    public function it_shifts_time_backward_by_negative_hours(): void
    {
        $result = SleepHelper::shift('02:00', -2);

        $this->assertEquals('00:00', $result);
    }

    #[Test]
    public function it_shifts_time_forward_across_midnight(): void
    {
        $result = SleepHelper::shift('23:00', 3);

        $this->assertEquals('02:00', $result);
    }

    #[Test]
    public function it_shifts_time_by_zero_hours(): void
    {
        $result = SleepHelper::shift('14:30', 0);

        $this->assertEquals('14:30', $result);
    }

    #[Test]
    public function it_shifts_time_with_minutes(): void
    {
        $result = SleepHelper::shift('14:30', 2);

        $this->assertEquals('16:30', $result);
    }
}
