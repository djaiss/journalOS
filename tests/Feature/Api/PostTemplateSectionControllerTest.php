<?php

namespace Tests\Feature\Api;

use App\Models\PostTemplate;
use App\Models\PostTemplateSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostTemplateSectionControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_post_template_section(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $postTemplate = PostTemplate::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->json('POST', '/api/post-templates/'.$postTemplate->id.'/sections', [
            'label' => 'Title',
            'position' => null,
        ]);

        $response->assertStatus(201);

        $postTemplateSection = PostTemplateSection::latest('id')->first();

        $this->assertEquals(
            [
                'id' => $postTemplateSection->id,
                'object' => 'post template section',
                'label' => 'Title',
                'position' => 1,
            ],
            $response->json()
        );
    }

    /** @test */
    public function it_cant_create_a_post_template_section(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $postTemplate = PostTemplate::factory()->create();

        $response = $this->json('POST', '/api/post-templates/' . $postTemplate->id . '/sections', [
            'label' => 'Title',
            'position' => null,
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_updates_a_post_template_section(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $postTemplate = PostTemplate::factory()->create([
            'user_id' => $user->id,
        ]);

        $section = PostTemplateSection::factory()->create([
            'post_template_id' => $postTemplate->id,
            'label' => 'Daily meditation',
            'position' => 1,
        ]);

        $response = $this->json('PUT', '/api/post-templates/' . $postTemplate->id.'/sections/'.$section->id, [
            'label' => 'Daily meditation updated',
            'position' => null,
        ]);

        $response->assertStatus(200);

        $this->assertEquals(
            [
                'id' => $section->id,
                'object' => 'post template section',
                'label' => 'Daily meditation updated',
                'position' => 1,
            ],
            $response->json()
        );
    }

    /** @test */
    public function it_cant_update_a_post_template_section(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $postTemplate = PostTemplate::factory()->create();

        $section = PostTemplateSection::factory()->create([
            'post_template_id' => $postTemplate->id,
        ]);

        $response = $this->json('PUT', '/api/post-templates/' . $postTemplate->id.'/sections/'.$section->id, [
            'label' => 'Daily meditation',
            'position' => null,
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_deletes_a_post_template_section(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $postTemplate = PostTemplate::factory()->create([
            'user_id' => $user->id,
        ]);

        $section = PostTemplateSection::factory()->create([
            'post_template_id' => $postTemplate->id,
        ]);

        $response = $this->json('DELETE', '/api/post-templates/' . $postTemplate->id.'/sections/'.$section->id);

        $response->assertStatus(200);

        $this->assertEquals(
            [
                'status' => 'success',
            ],
            $response->json()
        );
    }

    /** @test */
    public function it_cant_delete_a_post_template_section(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $postTemplate = PostTemplate::factory()->create();

        $section = PostTemplateSection::factory()->create([
            'post_template_id' => $postTemplate->id,
        ]);

        $response = $this->json('DELETE', '/api/post-templates/' . $postTemplate->id.'/sections/'.$section->id);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_lists_all_the_post_template_sections(): void
    {
        $user = User::factory()->create();
        $postTemplate = PostTemplate::factory()->create([
            'user_id' => $user->id,
        ]);
        $section = PostTemplateSection::factory()->create([
            'post_template_id' => $postTemplate->id,
            'label' => 'Daily meditation',
            'position' => 1,
        ]);
        $secondSection = PostTemplateSection::factory()->create([
            'post_template_id' => $postTemplate->id,
            'label' => 'Daily meditation 2',
            'position' => 2,
        ]);
        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/post-templates/' . $postTemplate->id . '/sections');

        $response->assertStatus(200);

        $this->assertEquals(
            $response->json(),
            [
                0 => [
                    'id' => $section->id,
                    'object' => 'post template section',
                    'label' => 'Daily meditation',
                    'position' => 1,
                ],
                1 => [
                    'id' => $secondSection->id,
                    'object' => 'post template section',
                    'label' => 'Daily meditation 2',
                    'position' => 2,
                ],
            ]
        );
    }
}
