<?php

declare(strict_types = 1);

namespace App\Enums;

enum BookStatus: string
{
    case STARTED = 'started';
    case CONTINUED = 'continued';
    case FINISHED = 'finished';
}
