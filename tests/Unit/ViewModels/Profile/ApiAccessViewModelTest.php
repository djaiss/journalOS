<?php

namespace Tests\Unit\ViewModels\Profile;

use App\Http\ViewModels\Profile\ApiAccessViewModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ApiAccessViewModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_gets_all_the_tokens_needed_for_the_index_view(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));
        $user = User::factory()->create();
        $this->actingAs($user);

        $token = DB::table('personal_access_tokens')->insertGetId([
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'name' => 'Test Token',
            'token' => 'test',
            'created_at' => Carbon::now(),
            'last_used_at' => Carbon::now(),
        ]);

        $array = ApiAccessViewModel::index();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('tokens', $array);

        $this->assertEquals(
            [
                'id' => $token,
                'name' => 'Test Token',
                'last_used' => '0 seconds ago',
            ],
            $array['tokens']->toArray()[0]
        );
    }
}
