<?php

namespace Tests\Unit\Services;

use App\Models\PostTemplate;
use App\Models\PostTemplateSection;
use App\Models\User;
use App\Services\UpdatePostTemplateSection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UpdatePostTemplateSectionTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_updates_a_post_template_section(): void
    {
        $user = User::factory()->create();
        $this->be($user);

        $postTemplate = PostTemplate::factory()->create([
            'user_id' => $user->id,
        ]);

        $postTemplateSection = PostTemplateSection::factory()->create([
            'post_template_id' => $postTemplate->id,
        ]);

        $postTemplateSection = (new UpdatePostTemplateSection(
            postTemplateSection: $postTemplateSection,
            label: 'Business',
        ))->execute();

        $this->assertDatabaseHas('post_template_sections', [
            'id' => $postTemplateSection->id,
            'post_template_id' => $postTemplate->id,
            'label' => 'Business',
        ]);

        $this->assertInstanceOf(
            PostTemplateSection::class,
            $postTemplateSection
        );
    }

    public function it_cant_update_a_post_template_section(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $this->be($user);

        $postTemplate = PostTemplate::factory()->create();

        $postTemplateSection = PostTemplateSection::factory()->create([
            'post_template_id' => $postTemplate->id,
        ]);

        (new UpdatePostTemplateSection(
            postTemplateSection: $postTemplateSection,
            label: 'Business',
        ))->execute();
    }
}
