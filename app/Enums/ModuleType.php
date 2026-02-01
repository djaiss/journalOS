<?php

declare(strict_types = 1);

namespace App\Enums;

enum ModuleType: string
{
    case BODY_HEALTH = 'body and health';
    case MIND_EMOTION = 'mind and emotion';
    case WORK_RESPONSABILITIES = 'work and responsibilities';
    case RELATIONSHIPS_SOCIAL_LIFE = 'relationships and social life';
    case MOVEMENT_PLACES = 'movement and places';
}
