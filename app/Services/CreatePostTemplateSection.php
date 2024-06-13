<?php

namespace App\Services;

use App\Models\PostTemplate;
use App\Models\PostTemplateSection;

class CreatePostTemplateSection extends BaseService
{
    private PostTemplateSection $postTemplateSection;

    public function __construct(
        public PostTemplate $postTemplate,
        public ?string $label,
        public ?string $labelTranslationKey,
        public ?int $position,
        public bool $canBeDeleted,
    ) {
    }

    public function execute(): PostTemplateSection
    {
        $postTemplate = auth()->user()->postTemplates()
            ->findOrFail($this->postTemplate->id);

        // determine the new position of the template page
        $newPosition = $postTemplate->postTemplateSections()
            ->max('position');
        $newPosition++;

        $this->postTemplateSection = PostTemplateSection::create([
            'post_template_id' => $this->postTemplate->id,
            'label' => $this->label,
            'label_translation_key' => $this->labelTranslationKey,
            'position' => $newPosition,
            'can_be_deleted' => $this->canBeDeleted,
        ]);

        return $this->postTemplateSection;
    }
}
