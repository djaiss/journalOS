<?php

namespace App\Services;

use App\Models\PostTemplate;

class CreatePostTemplate extends BaseService
{
    private PostTemplate $postTemplate;

    public function __construct(
        public ?string $label,
        public ?string $labelTranslationKey,
        public bool $canBeDeleted,
    ) {
    }

    public function execute(): PostTemplate
    {
        // determine the new position of the template page
        $newPosition = auth()->user()->postTemplates()
            ->max('position');
        $newPosition++;

        $this->postTemplate = PostTemplate::create([
            'user_id' => auth()->user()->id,
            'label' => $this->label,
            'label_translation_key' => $this->labelTranslationKey,
            'position' => $newPosition,
            'can_be_deleted' => $this->canBeDeleted,
        ]);

        return $this->postTemplate;
    }
}
