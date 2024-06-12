<?php

namespace Tests\Unit\Services;

use App\Models\PostTemplate;
use App\Models\User;
use App\Services\UpdatePostTemplatePosition;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UpdatePostTemplatePositionTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_updates_a_post_template_position(): void
    {
        $user = User::factory()->create();
        $this->be($user);

        $postTemplate1 = PostTemplate::factory()->create([
            'user_id' => $user->id,
            'position' => 1,
        ]);
        $postTemplate2 = PostTemplate::factory()->create([
            'user_id' => $user->id,
            'position' => 2,
        ]);
        $postTemplate3 = PostTemplate::factory()->create([
            'user_id' => $user->id,
            'position' => 3,
        ]);
        $postTemplate4 = PostTemplate::factory()->create([
            'user_id' => $user->id,
            'position' => 4,
        ]);

        $postTemplate = (new UpdatePostTemplatePosition(
            postTemplate: $postTemplate2,
            newPosition: 3,
        ))->execute();

        $this->assertDatabaseHas('post_templates', [
            'id' => $postTemplate1->id,
            'user_id' => $user->id,
            'position' => 1,
        ]);
        $this->assertDatabaseHas('post_templates', [
            'id' => $postTemplate3->id,
            'user_id' => $user->id,
            'position' => 2,
        ]);
        $this->assertDatabaseHas('post_templates', [
            'id' => $postTemplate4->id,
            'user_id' => $user->id,
            'position' => 4,
        ]);
        $this->assertDatabaseHas('post_templates', [
            'id' => $postTemplate2->id,
            'user_id' => $user->id,
            'position' => 3,
        ]);

        $postTemplate = (new UpdatePostTemplatePosition(
            postTemplate: $postTemplate2,
            newPosition: 2,
        ))->execute();

        $this->assertDatabaseHas('post_templates', [
            'id' => $postTemplate1->id,
            'user_id' => $user->id,
            'position' => 1,
        ]);
        $this->assertDatabaseHas('post_templates', [
            'id' => $postTemplate3->id,
            'user_id' => $user->id,
            'position' => 3,
        ]);
        $this->assertDatabaseHas('post_templates', [
            'id' => $postTemplate4->id,
            'user_id' => $user->id,
            'position' => 4,
        ]);
        $this->assertDatabaseHas('post_templates', [
            'id' => $postTemplate2->id,
            'user_id' => $user->id,
            'position' => 2,
        ]);

        $this->assertInstanceOf(
            PostTemplate::class,
            $postTemplate
        );
    }
}
