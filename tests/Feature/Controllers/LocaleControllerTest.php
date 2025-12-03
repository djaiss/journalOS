<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\App;

it('can update locale', function (): void {
    $response = $this->from('/')
        ->put('/locale', [
            'locale' => 'fr',
        ]);

    $response->assertRedirect('/');
    expect(session('locale'))->toEqual('fr');
    expect(App::getLocale())->toEqual('fr');
});


it('updates authenticated user locale', function (): void {
    $user = User::factory()->create([
        'locale' => 'en',
    ]);

    $response = $this->actingAs($user)
        ->from('/')
        ->put('/locale', [
            'locale' => 'fr',
        ]);

    $response->assertRedirect('/');
    expect(session('locale'))->toEqual('fr');
    expect(App::getLocale())->toEqual('fr');
    expect($user->fresh()->locale)->toEqual('fr');
});

it('validates locale input', function (): void {
    $response = $this->from('/')
        ->put('/locale', [
            'locale' => 'invalid',
        ]);

    $response->assertRedirect('/');
    $response->assertSessionHasErrors(['locale']);
});

it('requires locale', function (): void {
    $response = $this->from('/')
        ->put('/locale', [
            'locale' => '',
        ]);

    $response->assertRedirect('/');
    $response->assertSessionHasErrors(['locale']);
});

it('handles null locale', function (): void {
    $response = $this->from('/')
        ->put('/locale', [
            'locale' => null,
        ]);

    $response->assertRedirect('/');
    $response->assertSessionHasErrors(['locale']);
});

it('handles missing locale', function (): void {
    $response = $this->from('/')
        ->put('/locale', []);

    $response->assertRedirect('/');
    $response->assertSessionHasErrors(['locale']);
});

it('preserves previous locale on validation failure', function (): void {
    App::setLocale('en');
    session()->put('locale', 'en');

    $response = $this->from('/')
        ->put('/locale', [
            'locale' => 'invalid',
        ]);

    $response->assertRedirect('/');
    $response->assertSessionHasErrors(['locale']);
    expect(App::getLocale())->toEqual('en');
    expect(session('locale'))->toEqual('en');
});

it('preserves authenticated user locale on validation failure', function (): void {
    $user = User::factory()->create([
        'locale' => 'en',
    ]);

    $response = $this->actingAs($user)
        ->from('/')
        ->put('/locale', [
            'locale' => 'invalid',
        ]);

    $response->assertRedirect('/');
    $response->assertSessionHasErrors(['locale']);
    expect($user->fresh()->locale)->toEqual('en');
});
