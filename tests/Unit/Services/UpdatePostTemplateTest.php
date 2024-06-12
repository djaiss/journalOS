<?php

namespace Tests\Unit\Services;

use App\Models\PostTemplate;
use App\Models\User;
use App\Services\UpdatePostTemplate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UpdatePostTemplateTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_updates_a_post_template(): void
    {
        $user = User::factory()->create();
        $this->be($user);

        $postTemplate = PostTemplate::factory()->create([
            'user_id' => $user->id,
            'label' => 'Nice journal',
            'label_translation_key' => 'nice',
            'can_be_deleted' => false,
        ]);

        $postTemplate = (new UpdatePostTemplate(
            postTemplate: $postTemplate,
            label: 'Business',
        ))->execute();

        $this->assertDatabaseHas('post_templates', [
            'id' => $postTemplate->id,
            'user_id' => $user->id,
            'label' => 'Business',
            'position' => 1,
        ]);

        $this->assertInstanceOf(
            PostTemplate::class,
            $postTemplate
        );
    }

    public function it_cant_update_a_post_template(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $this->be($user);

        $postTemplate = PostTemplate::factory()->create([]);

        (new UpdatePostTemplate(
            postTemplate: $postTemplate,
            label: 'Business',
        ))->execute();
    }
}
