<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Create an organization for a user.
 * The user will be added to the organization as the first user.
 */
final class CreateOrganization
{
    private Organization $organization;

    public function __construct(
        public User $user,
        public string $organizationName,
    ) {}

    public function execute(): Organization
    {
        $this->validate();
        $this->create();
        $this->generateSlug();
        $this->addFirstUser();
        $this->log();

        return $this->organization;
    }

    private function validate(): void
    {
        // make sure the organization name doesn't contain any special characters
        if (in_array(preg_match('/^[a-zA-Z0-9\s\-_]+$/', $this->organizationName), [0, false], true)) {
            throw ValidationException::withMessages([
                'organization_name' => 'Organization name can only contain letters, numbers, spaces, hyphens and underscores',
            ]);
        }
    }

    private function create(): void
    {
        $this->organization = Organization::create([
            'name' => $this->organizationName,
        ]);
    }

    private function generateSlug(): void
    {
        $slug = $this->organization->id . '-' . Str::of($this->organizationName)->slug('-');

        $this->organization->slug = $slug;
        $this->organization->save();
    }

    private function addFirstUser(): void
    {
        $this->user->organizations()->attach($this->organization->id, [
            'joined_at' => now(),
        ]);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            organization: $this->organization,
            user: $this->user,
            action: 'organization_creation',
            description: sprintf('Created an organization called %s', $this->organizationName),
        )->onQueue('low');
    }
}
