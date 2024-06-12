<?php

namespace App\Cache;

use App\Helpers\CacheHelper;
use App\Models\Action;
use App\Models\Organization;
use App\Models\User;
use App\Traits\CacheIdentifier;
use Illuminate\Support\Collection;

final class UserPermissionsCache extends CacheHelper
{
    use CacheIdentifier;

    protected string $key = 'user-permissions:%s';

    protected int $ttl = 604800;

    public function __construct(
        protected readonly User $user,
        protected readonly Organization $organization,
    ) {
        $this->identifier = $user->id . '-' . $organization->id;
    }

    public static function make(User $user, Organization $organization): static
    {
        return new self($user, $organization);
    }

    protected function generate(): Collection
    {
        $permission = $this->user->getPermissionInOrganization($this->organization);

        return $permission->actions
            ->map(fn (Action $action) => [
                'identifier' => $action->identifier,
            ]);
    }
}
