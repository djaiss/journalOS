<?php

namespace Tests\Unit\Services;

use App\Models\PostTemplate;
use App\Models\User;
use App\Services\CreatePostTemplate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CreatePostTemplateTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_creates_a_post_template(): void
    {
        $user = User::factory()->create();
        $this->be($user);

        $postTemplate = (new CreatePostTemplate(
            label: 'Nice journal',
            labelTranslationKey: 'nice',
            canBeDeleted: false,
        ))->execute();

        $this->assertDatabaseHas('post_templates', [
            'id' => $postTemplate->id,
            'user_id' => $user->id,
            'label' => 'Nice journal',
            'position' => 1,
        ]);

        $this->assertInstanceOf(
            PostTemplate::class,
            $postTemplate
        );
    }
}
