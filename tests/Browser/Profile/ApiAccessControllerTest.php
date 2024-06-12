<?php

namespace Tests\Browser\Profile;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ApiAccessControllerTest extends DuskTestCase
{
    use DatabaseTruncation;

    /** @test */
    public function we_can_manage_api_tokens(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user): void {
            // create a team
            $browser->loginAs($user)
                ->visit('/settings/keys')
                ->assertNotPresent('@main-cta-add-key')
                ->click('@blank-cta-add-key')
                ->assertPathIs('/settings/keys/new')
                ->type('token_name', 'iPhone')
                ->click('@cta-create')
                ->assertPathIs('/settings/keys')
                ->assertSee('iPhone')
                ->assertNotPresent('@blank-cta-add-key');

            // revoke the token
            $tokenId = $user->tokens()->first()->id;

            $browser->visit('/settings/keys')
                ->click('@cta-revoke-key-' . $tokenId)
                ->acceptDialog()
                ->pause(150)
                ->assertDontSee('iPhone');
        });
    }
}
