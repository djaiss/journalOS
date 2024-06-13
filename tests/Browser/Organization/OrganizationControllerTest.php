<?php

namespace Tests\Browser\Organization;

use App\Models\Organization;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class OrganizationControllerTest extends DuskTestCase
{
    use DatabaseTruncation;

    /** @test */
    public function we_can_create_an_organization(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user): void {
            // create a team
            $browser->loginAs($user)
                ->visit('/dashboard')
                ->click('@cta-create-organization')
                ->assertPathIs('/organizations/new')
                ->type('organization_name', 'Dunder Mifflin')
                ->click('@cta-create')
                ->assertPathIs('/organizations/'.Organization::latest()->first()->id)
                ->assertSeeIn('@header-organization-name', 'Dunder Mifflin')
                ->visit('/dashboard')
                ->assertSee('Dunder Mifflin');
        });
    }
}
