<?php

declare(strict_types=1);

namespace App\Http\ViewModels\Organizations;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Collection;

final readonly class OrganizationIndexViewModel
{
    public function __construct(
        private User $user,
    ) {}

    public function organizations(): Collection
    {
        return $this->user->organizations()
            ->get()
            ->map(fn(Organization $organization) => (object) [
                'id' => $organization->id,
                'name' => $organization->name,
                'slug' => $organization->slug,
                'avatar' => $organization->getAvatar(),
            ]);
    }
}
