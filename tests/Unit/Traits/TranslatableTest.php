<?php

namespace Tests\Unit\Traits;

use App\Models\PostTemplate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TranslatableTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_translates(): void
    {
        $postTemplate = PostTemplate::factory()->create([
            'label' => 'this is the real name',
            'label_translation_key' => 'permission.label',
        ]);

        $this->assertEquals(
            'this is the real name',
            $postTemplate->label
        );

        $postTemplate = PostTemplate::factory()->create([
            'label' => null,
            'label_translation_key' => 'permission.label',
        ]);

        $this->assertEquals(
            'permission.label',
            $postTemplate->label
        );
    }
}
