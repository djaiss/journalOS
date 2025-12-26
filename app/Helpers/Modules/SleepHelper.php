<?php

declare(strict_types=1);

namespace App\Helpers\Modules;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;

final class SleepHelper
{
    /**
     * Get the range of times for the sleep tracking.
     *
     * @param string $start
     * @param string|null $selected
     * @return Collection
     */
    public static function range(string $start, ?string $selected): Collection
    {
        $startTime = Date::createFromFormat('H:i', $start);

        return collect(range(1, 5))->map(function ($offset) use ($startTime, $selected) {
            $time = $startTime->copy()->addHours($offset)->format('H:i');

            return [
                'time' => $time,
                'is_selected' => $time === $selected,
            ];
        });
    }

    /**
     * Shift the time by the given number of hours.
     *
     * @param string $time
     * @param int $hours
     * @return string
     */
    public static function shift(string $time, int $hours): string
    {
        return Date::createFromFormat('H:i', $time)
            ->addHours($hours)
            ->format('H:i');
    }
}
