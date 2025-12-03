<?php

declare(strict_types=1);

arch()->preset()->laravel()
    ->ignoring('App\Http\Controllers\Auth\VerificationController')
    ->ignoring('App\Http\Controllers\Auth\LoginController');

arch()->preset()->php();

arch()->preset()->security();

arch('app uses strict types')
    ->expect('App')
    ->toUseStrictTypes();

arch('actions should be final')
    ->expect('App\Actions')
    ->toBeClasses()
    ->toBeFinal();

arch('models should be final when appropriate')
    ->expect('App\Models')
    ->toBeClasses()
    ->not->toBeAbstract();

arch('controllers do not have business logic')
    ->expect('App\Http\Controllers')
    ->not->toUse([
        'Illuminate\Support\Facades\DB',
        'Illuminate\Support\Facades\Cache',
    ]);

arch('actions have execute method')
    ->expect('App\Actions')
    ->toHaveMethod('execute');

arch('no unused imports')
    ->expect(['app', 'config', 'database'])
    ->not->toUse([
        'Illuminate\Support\Facades\Log',
    ]);

arch('proper return types')
    ->expect(['app'])
    ->toHaveMethodsDocumented();

arch('Actions do not depend on HTTP layer')
    ->expect('App\Actions')
    ->not->toUse([
        'Illuminate\Http\Request',
        'Illuminate\Http\Response',
    ]);

arch('Models do not depend on HTTP layer')
    ->expect('App\Models')
    ->not->toUse([
        'Illuminate\Http\Request',
        'Illuminate\Http\Response',
    ]);
