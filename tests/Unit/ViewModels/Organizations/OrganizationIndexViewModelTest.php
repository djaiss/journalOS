<?php

declare(strict_types=1);

namespace Tests\Unit\ViewModels\Organizations;

use App\Http\ViewModels\Organizations\OrganizationIndexViewModel;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class OrganizationIndexViewModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_the_correct_organizations(): void
    {
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

        $this->assertCount(2, $organizations);

        $firstOrganization = $organizations->first();
        $this->assertTrue(property_exists($firstOrganization, 'id'));
        $this->assertTrue(property_exists($firstOrganization, 'name'));
        $this->assertTrue(property_exists($firstOrganization, 'slug'));
        $this->assertTrue(property_exists($firstOrganization, 'avatar'));

        $this->assertEquals($organization1->id, $firstOrganization->id);
        $this->assertEquals('Dunder Mifflin', $organization1->name);
    }
}
