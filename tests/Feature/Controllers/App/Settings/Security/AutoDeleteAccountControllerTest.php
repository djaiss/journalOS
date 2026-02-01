<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App\Settings\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class AutoDeleteAccountControllerTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_updates_auto_delete_account_setting_to_true(): void
    {
        $user = User::factory()->create([
            'auto_delete_account' => false,
        ]);

        $this->actingAs($user);

        $response = $this->put(route('settings.security.auto-delete.update'), [
            'auto_delete_account' => 'yes',
        ]);

        $response->assertRedirect(route('settings.security.index'));
        $response->assertSessionHas('status', trans('Changes saved'));

        $this->assertTrue($user->fresh()->auto_delete_account);
    }

    #[Test]
    public function it_updates_auto_delete_account_setting_to_false(): void
    {
        $user = User::factory()->create([
            'auto_delete_account' => true,
        ]);

        $this->actingAs($user);

        $response = $this->put(route('settings.security.auto-delete.update'), [
            'auto_delete_account' => 'no',
        ]);

        $response->assertRedirect(route('settings.security.index'));
        $response->assertSessionHas('status', trans('Changes saved'));

        $this->assertFalse($user->fresh()->auto_delete_account);
    }
}
