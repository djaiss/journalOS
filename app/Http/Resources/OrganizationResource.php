<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Organization;

/**
 * @mixin Organization
 */
final class OrganizationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'organization',
            'id' => (string) $this->id,
            'attributes' => [
                'name' => $this->name,
                'slug' => $this->slug,
                'avatar' => $this->getAvatar(),
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at->timestamp,
            ],
            'links' => [
                'self' => route('api.organization.show', $this->id),
            ],
        ];
    }
}
