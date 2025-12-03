<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\JobLevel;

/**
 * @mixin JobLevel
 */
final class JobLevelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'job_level',
            'id' => (string) $this->id,
            'attributes' => [
                'organization_id' => $this->organization_id,
                'job_discipline_id' => $this->job_discipline_id,
                'name' => $this->name,
                'description' => $this->description,
                'slug' => $this->slug,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at->timestamp,
            ],
            'links' => [
                'self' => "/api/organizations/{$this->organization_id}/settings/job-families/{$this->jobDiscipline->job_family_id}/job-disciplines/{$this->job_discipline_id}/job-levels/{$this->id}",
            ],
        ];
    }
}
