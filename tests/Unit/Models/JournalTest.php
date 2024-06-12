<?php

namespace Tests\Unit\Models;

use App\Models\Journal;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_one_user(): void
    {
        $journal = Journal::factory()->create();

        $this->assertTrue($journal->user()->exists());
    }

    /** @test */
    public function it_has_many_posts(): void
    {
        $journal = Journal::factory()->create();
        Post::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $this->assertTrue($journal->posts()->exists());
    }
}
