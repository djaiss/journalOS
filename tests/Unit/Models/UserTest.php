<?php

namespace Tests\Unit\Models;

use App\Models\Journal;
use App\Models\PostTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_many_journals(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($user->journals()->exists());
    }

    /** @test */
    public function it_has_many_post_templates(): void
    {
        $user = User::factory()->create();
        PostTemplate::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($user->postTemplates()->exists());
    }
}
