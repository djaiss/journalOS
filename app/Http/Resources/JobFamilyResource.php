<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\JobFamily;

/**
 * @mixin JobFamily
 */
final class JobFamilyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'job_family',
            'id' => (string) $this->id,
            'attributes' => [
                'name' => $this->name,
                'description' => $this->description,
                'slug' => $this->slug,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at->timestamp,
            ],
            'links' => [
                'self' => "/api/organizations/{$this->organization->id}/settings/job-families/{$this->id}",
            ],
        ];
    }
}
