<?php

namespace Tests\Unit\Services;

use App\Models\PostTemplate;
use App\Models\PostTemplateSection;
use App\Models\User;
use App\Services\UpdatePostTemplateSectionPosition;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UpdatePostTemplateSectionPositionTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_updates_a_post_template_section_position(): void
    {
        $user = User::factory()->create();
        $this->be($user);

        $postTemplate = PostTemplate::factory()->create([
            'user_id' => $user->id,
        ]);
        $postTemplateSection = PostTemplateSection::factory()->create([
            'post_template_id' => $postTemplate->id,
            'position' => 2,
        ]);

        $postTemplateSection1 = PostTemplateSection::factory()->create([
            'post_template_id' => $postTemplate->id,
            'position' => 1,
        ]);
        $postTemplateSection3 = PostTemplateSection::factory()->create([
            'post_template_id' => $postTemplate->id,
            'position' => 3,
        ]);
        $postTemplateSection4 = PostTemplateSection::factory()->create([
            'post_template_id' => $postTemplate->id,
            'position' => 4,
        ]);

        $postTemplateSection = (new UpdatePostTemplateSectionPosition(
            postTemplateSection: $postTemplateSection,
            newPosition: 3,
        ))->execute();

        $this->assertDatabaseHas('post_template_sections', [
            'id' => $postTemplateSection1->id,
            'post_template_id' => $postTemplate->id,
            'position' => 1,
        ]);
        $this->assertDatabaseHas('post_template_sections', [
            'id' => $postTemplateSection3->id,
            'post_template_id' => $postTemplate->id,
            'position' => 2,
        ]);
        $this->assertDatabaseHas('post_template_sections', [
            'id' => $postTemplateSection4->id,
            'post_template_id' => $postTemplate->id,
            'position' => 4,
        ]);
        $this->assertDatabaseHas('post_template_sections', [
            'id' => $postTemplateSection->id,
            'post_template_id' => $postTemplate->id,
            'position' => 3,
        ]);

        $request['new_position'] = 2;

        $postTemplateSection = (new UpdatePostTemplateSectionPosition(
            postTemplateSection: $postTemplateSection,
            newPosition: 2,
        ))->execute();

        $this->assertDatabaseHas('post_template_sections', [
            'id' => $postTemplateSection1->id,
            'post_template_id' => $postTemplate->id,
            'position' => 1,
        ]);
        $this->assertDatabaseHas('post_template_sections', [
            'id' => $postTemplateSection3->id,
            'post_template_id' => $postTemplate->id,
            'position' => 3,
        ]);
        $this->assertDatabaseHas('post_template_sections', [
            'id' => $postTemplateSection4->id,
            'post_template_id' => $postTemplate->id,
            'position' => 4,
        ]);
        $this->assertDatabaseHas('post_template_sections', [
            'id' => $postTemplateSection->id,
            'post_template_id' => $postTemplate->id,
            'position' => 2,
        ]);

        $this->assertInstanceOf(
            PostTemplateSection::class,
            $postTemplateSection
        );
    }
}
