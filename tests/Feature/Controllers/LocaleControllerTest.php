<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class LocaleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_update_locale(): void
    {
        $response = $this->from('/')
            ->put('/locale', [
                'locale' => 'fr',
            ]);

        $response->assertRedirect('/');
        $this->assertEquals('fr', session('locale'));
        $this->assertEquals('fr', App::getLocale());
    }

    public function test_it_updates_authenticated_user_locale(): void
    {
        $user = User::factory()->create([
            'locale' => 'en',
        ]);

        $response = $this->actingAs($user)
            ->from('/')
            ->put('/locale', [
                'locale' => 'fr',
            ]);

        $response->assertRedirect('/');
        $this->assertEquals('fr', session('locale'));
        $this->assertEquals('fr', App::getLocale());
        $this->assertEquals('fr', $user->fresh()->locale);
    }

    public function test_it_validates_locale_input(): void
    {
        $response = $this->from('/')
            ->put('/locale', [
                'locale' => 'invalid',
            ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['locale']);
    }

    public function test_it_requires_locale(): void
    {
        $response = $this->from('/')
            ->put('/locale', [
                'locale' => '',
            ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['locale']);
    }

    public function test_it_handles_null_locale(): void
    {
        $response = $this->from('/')
            ->put('/locale', [
                'locale' => null,
            ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['locale']);
    }

    public function test_it_handles_missing_locale(): void
    {
        $response = $this->from('/')
            ->put('/locale', []);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['locale']);
    }

    public function test_it_preserves_previous_locale_on_validation_failure(): void
    {
        App::setLocale('en');
        session()->put('locale', 'en');

        $response = $this->from('/')
            ->put('/locale', [
                'locale' => 'invalid',
            ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['locale']);
        $this->assertEquals('en', App::getLocale());
        $this->assertEquals('en', session('locale'));
    }

    public function test_it_preserves_authenticated_user_locale_on_validation_failure(): void
    {
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
        $this->assertEquals('en', $user->fresh()->locale);
    }
}
