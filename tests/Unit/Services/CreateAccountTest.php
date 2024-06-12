<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\CreateAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateAccountTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_an_account(): void
    {
        $user = (new CreateAccount(
            email: 'dwight@dundermifflin.com',
            password: 'johnny',
            name: 'Dwight Schrute',
        ))->execute();

        $this->assertInstanceOf(
            User::class,
            $user
        );

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Dwight Schrute',
            'email' => 'dwight@dundermifflin.com',
        ]);
    }
}
