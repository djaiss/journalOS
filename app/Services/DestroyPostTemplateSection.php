<?php

namespace App\Services;

use App\Models\PostTemplateSection;

class DestroyPostTemplateSection extends BaseService
{
    public function __construct(
        public PostTemplateSection $postTemplateSection,
    ) {
    }

    public function execute(): void
    {
        $this->postTemplateSection = PostTemplateSection::findOrFail($this->postTemplateSection->id);

        $this->postTemplateSection->postTemplate()->where('user_id', auth()->user()->id)->firstOrFail();

        $this->postTemplateSection->delete();
    }
}
