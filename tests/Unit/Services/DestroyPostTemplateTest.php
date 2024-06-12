<?php

namespace Tests\Unit\Services;

use App\Models\PostTemplate;
use App\Models\User;
use App\Services\DestroyPostTemplate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DestroyPostTemplateTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_destroys_a_post_template(): void
    {
        $user = User::factory()->create();
        $this->be($user);

        $postTemplate = PostTemplate::factory()->create([
            'user_id' => $user->id,
        ]);

        (new DestroyPostTemplate(
            postTemplate: $postTemplate,
        ))->execute();

        $this->assertDatabaseMissing('post_templates', [
            'id' => $postTemplate->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function it_cant_destroy_a_post_template(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $this->be($user);

        $postTemplate = PostTemplate::factory()->create();

        (new DestroyPostTemplate(
            postTemplate: $postTemplate,
        ))->execute();
    }
}
