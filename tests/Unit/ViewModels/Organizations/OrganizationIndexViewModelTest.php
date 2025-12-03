<?php

declare(strict_types=1);

use App\Http\ViewModels\Organizations\OrganizationIndexViewModel;
use App\Models\User;
use App\Models\Organization;

it('returns the correct organizations', function (): void {
    $user = User::factory()->create();
    $organization1 = Organization::factory()->create([
        'name' => 'Dunder Mifflin',
        'slug' => 'dunder-mifflin',
    ]);
    $organization2 = Organization::factory()->create([
        'name' => 'Dunder Mifflin Paper Company',
        'slug' => 'dunder-mifflin-paper-company',
    ]);

    $user->organizations()->attach($organization1->id, [
        'joined_at' => now(),
    ]);
    $user->organizations()->attach($organization2->id, [
        'joined_at' => now(),
    ]);

    $viewModel = new OrganizationIndexViewModel(
        user: $user,
    );

    $organizations = $viewModel->organizations();

    expect($organizations)->toHaveCount(2);

    $firstOrganization = $organizations->first();
    expect(property_exists($firstOrganization, 'id'))->toBeTrue();
    expect(property_exists($firstOrganization, 'name'))->toBeTrue();
    expect(property_exists($firstOrganization, 'slug'))->toBeTrue();
    expect(property_exists($firstOrganization, 'avatar'))->toBeTrue();

    expect($firstOrganization->id)->toEqual($organization1->id);
    expect('Dunder Mifflin')->toEqual($organization1->name);
});
