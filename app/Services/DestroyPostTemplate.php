<?php

namespace App\Services;

use App\Models\PostTemplate;

class DestroyPostTemplate extends BaseService
{
    public function __construct(
        public PostTemplate $postTemplate,
    ) {
    }

    public function execute(): void
    {
        $this->postTemplate = auth()->user()->postTemplates()
            ->findOrFail($this->postTemplate->id);

        $this->postTemplate->delete();
    }
}
