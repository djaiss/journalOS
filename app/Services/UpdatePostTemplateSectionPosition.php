<?php

namespace App\Services;

use App\Models\PostTemplate;
use App\Models\PostTemplateSection;

class UpdatePostTemplateSectionPosition extends BaseService
{
    private PostTemplate $postTemplate;

    private int $pastPosition;

    public function __construct(
        public PostTemplateSection $postTemplateSection,
        public int $newPosition,
    ) {
    }

    public function execute(): PostTemplateSection
    {
        $this->validate();
        $this->updatePosition();

        return $this->postTemplateSection;
    }

    private function validate(): void
    {
        $this->postTemplateSection = PostTemplateSection::findOrFail($this->postTemplateSection->id);

        $this->postTemplate = $this->postTemplateSection
            ->postTemplate()
            ->where('user_id', auth()->user()->id)
            ->firstOrFail();

        $this->pastPosition = $this->postTemplateSection->position;
    }

    private function updatePosition(): void
    {
        if ($this->newPosition > $this->pastPosition) {
            $this->updateAscendingPosition();
        } else {
            $this->updateDescendingPosition();
        }

        $this->postTemplateSection
            ->update([
                'position' => $this->newPosition,
            ]);
    }

    private function updateAscendingPosition(): void
    {
        $this->postTemplate->postTemplateSections()
            ->where('position', '>', $this->pastPosition)
            ->where('position', '<=', $this->newPosition)
            ->decrement('position');
    }

    private function updateDescendingPosition(): void
    {
        $this->postTemplate->postTemplateSections()
            ->where('position', '>=', $this->newPosition)
            ->where('position', '<', $this->pastPosition)
            ->increment('position');
    }
}
