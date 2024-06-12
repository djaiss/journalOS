<?php

namespace Tests\Unit\Services;

use App\Models\PostTemplate;
use App\Models\PostTemplateSection;
use App\Models\User;
use App\Services\DestroyPostTemplateSection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DestroyPostTemplateSectionTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_destroys_a_post_template_section(): void
    {
        $user = User::factory()->create();
        $this->be($user);

        $postTemplate = PostTemplate::factory()->create([
            'user_id' => $user->id,
        ]);

        $postTemplateSection = PostTemplateSection::factory()->create([
            'post_template_id' => $postTemplate->id,
        ]);

        (new DestroyPostTemplateSection(
            postTemplateSection: $postTemplateSection,
        ))->execute();

        $this->assertDatabaseMissing('post_template_sections', [
            'id' => $postTemplateSection->id,
        ]);
    }

    /** @test */
    public function it_cant_destroy_a_post_template_section(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $this->be($user);

        $postTemplate = PostTemplate::factory()->create();

        $postTemplateSection = PostTemplateSection::factory()->create([
            'post_template_id' => $postTemplate->id,
        ]);

        (new DestroyPostTemplateSection(
            postTemplateSection: $postTemplateSection,
        ))->execute();
    }
}
