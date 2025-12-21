<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

final class TimeHelper
{
    /**
     * Format the time given as a string to the preferences of the logged user.
     *
     * @param string $time Time in "HH:MM" 24-hour format.
     */
    public static function format(string $time): string
    {
        $date = Date::createFromFormat('H:i', $time);

        return match (Auth::user()->time_format_24h) {
            true => $date->format('H:i'),
            false => $date->format('h:i A'),
        };
    }
}
