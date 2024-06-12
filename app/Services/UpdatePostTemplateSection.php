<?php

namespace App\Services;

use App\Models\PostTemplateSection;

class UpdatePostTemplateSection extends BaseService
{
    public function __construct(
        public PostTemplateSection $postTemplateSection,
        public string $label,
    ) {
    }

    public function execute(): PostTemplateSection
    {
        $this->postTemplateSection = PostTemplateSection::findOrFail($this->postTemplateSection->id);

        $this->postTemplateSection
            ->postTemplate()
            ->where('user_id', auth()->user()->id)
            ->firstOrFail();

        $this->postTemplateSection->label = $this->label;
        $this->postTemplateSection->save();

        return $this->postTemplateSection;
    }
}
