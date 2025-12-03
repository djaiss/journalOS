<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

/**
 * @mixin User
 */
final class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'user',
            'id' => (string) $this->id,
            'attributes' => [
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'nickname' => $this->nickname,
                'email' => $this->email,
                'locale' => $this->locale,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at->timestamp,
            ],
            'links' => [
                'self' => route('api.me'),
            ],
        ];
    }
}
