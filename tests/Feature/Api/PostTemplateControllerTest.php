<?php

namespace Tests\Feature\Api;

use App\Models\PostTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostTemplateControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_post_template(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->json('POST', '/api/post-templates', [
            'label' => 'New post template',
            'position' => null,
        ]);

        $response->assertStatus(201);

        $postTemplate = PostTemplate::latest('id')->first();

        $this->assertEquals(
            [
                'id' => $postTemplate->id,
                'object' => 'post template',
                'label' => 'New post template',
                'position' => 1,
            ],
            $response->json()
        );
    }

    /** @test */
    public function it_updates_a_post_template(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $postTemplate = PostTemplate::factory()->create([
            'user_id' => $user->id,
            'label' => 'Daily meditation',
            'position' => 1,
        ]);

        $response = $this->json('PUT', '/api/post-templates/'.$postTemplate->id, [
            'label' => 'Daily meditation updated',
            'position' => null,
        ]);

        $response->assertStatus(200);

        $this->assertEquals(
            [
                'id' => $postTemplate->id,
                'object' => 'post template',
                'label' => 'Daily meditation updated',
                'position' => 1,
            ],
            $response->json()
        );
    }

    /** @test */
    public function it_cant_update_a_post_template(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $postTemplate = PostTemplate::factory()->create([
            'label' => 'Daily meditation',
        ]);

        $response = $this->json('PUT', '/api/post-templates/'.$postTemplate->id, [
            'label' => 'Daily meditation',
            'position' => null,
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_deletes_a_post_template(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $postTemplate = PostTemplate::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->json('DELETE', '/api/post-templates/'.$postTemplate->id);

        $response->assertStatus(200);

        $this->assertEquals(
            [
                'status' => 'success',
            ],
            $response->json()
        );
    }

    /** @test */
    public function it_cant_delete_a_post_template(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $postTemplate = PostTemplate::factory()->create();

        $response = $this->json('DELETE', '/api/post-templates/'.$postTemplate->id);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_lists_all_the_post_templates(): void
    {
        $user = User::factory()->create();
        $postTemplate = $user->postTemplates()->create([
            'label' => 'Daily meditation',
            'position' => 1,
        ]);
        $secondJournal = $user->postTemplates()->create([
            'label' => 'Daily meditation 2',
            'position' => 2,
        ]);
        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/post-templates');

        $response->assertStatus(200);

        $this->assertEquals(
            $response->json(),
            [
                0 => [
                    'id' => $postTemplate->id,
                    'object' => 'post template',
                    'label' => 'Daily meditation',
                    'position' => 1,
                ],
                1 => [
                    'id' => $secondJournal->id,
                    'object' => 'post template',
                    'label' => 'Daily meditation 2',
                    'position' => 2,
                ],
            ]
        );
    }
}
