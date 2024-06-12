<?php

namespace App\Services;

use App\Models\PostTemplate;

class UpdatePostTemplatePosition extends BaseService
{
    private int $pastPosition;

    public function __construct(
        public PostTemplate $postTemplate,
        public int $newPosition,
    ) {
    }

    public function execute(): PostTemplate
    {
        $this->validate();
        $this->updatePosition();

        return $this->postTemplate;
    }

    private function validate(): void
    {
        $this->postTemplate = auth()->user()->postTemplates()
            ->findOrFail($this->postTemplate->id);

        $this->pastPosition = $this->postTemplate->position;
    }

    private function updatePosition(): void
    {
        if ($this->newPosition > $this->pastPosition) {
            $this->updateAscendingPosition();
        } else {
            $this->updateDescendingPosition();
        }

        $this->postTemplate
            ->update([
                'position' => $this->newPosition,
            ]);
    }

    private function updateAscendingPosition(): void
    {
        auth()->user()->postTemplates()
            ->where('position', '>', $this->pastPosition)
            ->where('position', '<=', $this->newPosition)
            ->decrement('position');
    }

    private function updateDescendingPosition(): void
    {
        auth()->user()->postTemplates()
            ->where('position', '>=', $this->newPosition)
            ->where('position', '<', $this->pastPosition)
            ->increment('position');
    }
}
