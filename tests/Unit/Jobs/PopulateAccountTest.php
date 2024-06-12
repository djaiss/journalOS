<?php

namespace Tests\Unit\Jobs;

use App\Jobs\PopulateAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PopulateAccountTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_populates_an_account(): void
    {
        $user = User::factory()->create();

        PopulateAccount::dispatch($user);

        $this->assertEquals(
            1,
            DB::table('journals')->count()
        );
    }
}
