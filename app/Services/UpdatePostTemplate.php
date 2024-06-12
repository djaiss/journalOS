<?php

namespace App\Services;

use App\Models\PostTemplate;

class UpdatePostTemplate extends BaseService
{
    public function __construct(
        public PostTemplate $postTemplate,
        public ?string $label,
    ) {
    }

    public function execute(): PostTemplate
    {
        $this->postTemplate = auth()->user()->postTemplates()
            ->findOrFail($this->postTemplate->id);

        $this->postTemplate->label = $this->label;
        $this->postTemplate->save();

        return $this->postTemplate;
    }
}
