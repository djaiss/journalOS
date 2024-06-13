<?php

namespace Tests\Unit\Services;

use App\Models\PostTemplate;
use App\Models\User;
use App\Services\CreatePostTemplateSection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CreatePostTemplateSectionTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_creates_a_post_template_section(): void
    {
        $user = User::factory()->create();
        $this->be($user);

        $postTemplate = PostTemplate::factory()->create([
            'user_id' => $user->id,
        ]);

        $postTemplateSection = (new CreatePostTemplateSection(
            postTemplate: $postTemplate,
            label: 'Business awesome',
            labelTranslationKey: 'business_awesome',
            position: 1,
            canBeDeleted: true,
        ))->execute();

        $this->assertDatabaseHas('post_template_sections', [
            'id' => $postTemplateSection->id,
            'post_template_id' => $postTemplate->id,
            'label' => 'Business awesome',
            'can_be_deleted' => 1,
        ]);

        $this->assertInstanceOf(
            PostTemplate::class,
            $postTemplate
        );
    }
}
