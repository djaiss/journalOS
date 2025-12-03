<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\CreateAccount;
use App\Actions\CreateJobDiscipline;
use App\Actions\CreateJobFamily;
use App\Actions\CreateJobLevel;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use App\Actions\CreateOrganization;

final class DatabaseSeeder extends Seeder
{
    private User $michael;

    private Organization $organization;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->createDunderMifflin();
        $this->validateEmail();
        $this->addOrganization();
        $this->createJobFamilies();
        $this->createJobDisciplines();
        $this->createJobLevels();
    }

    private function createDunderMifflin(): void
    {
        $this->michael = new CreateAccount(
            email: 'michael.scott@dundermifflin.com',
            password: 'password',
            firstName: 'Michael',
            lastName: 'Scott',
        )->execute();
    }

    private function validateEmail(): void
    {
        $this->michael->email_verified_at = now();
        $this->michael->save();
    }

    private function addOrganization(): void
    {
        $this->organization = new CreateOrganization(
            user: $this->michael,
            organizationName: 'Dunder Mifflin',
        )->execute();
    }

    private function createJobFamilies(): void
    {
        $jobFamilies = [
            'Engineering' => 'Engineering job family',
            'Design' => 'Design job family',
            'Product' => 'Product job family',
            'Marketing' => 'Marketing job family',
            'Sales' => 'Sales job family',
            'HR' => 'HR job family',
            'Finance' => 'Finance job family',
        ];

        foreach ($jobFamilies as $name => $description) {
            new CreateJobFamily(
                organization: $this->organization,
                user: $this->michael,
                jobFamilyName: $name,
                description: $description,
            )->execute();
        }
    }

    private function createJobDisciplines(): void
    {
        $jobDisciplines = [
            'Front end development',
            'Back end development',
            'Full stack development',
            'Mobile development',
            'UI/UX design',
            'Product management',
            'Project management',
            'Business development',
            'Sales',
            'Marketing',
            'HR',
            'Finance',
            'Legal',
            'Customer support',
            'Data analysis',
            'Data engineering',
            'Data science',
            'DevOps',
            'QA',
            'Security',
            'IT',
            'Network engineering',
            'System administration',
            'Database administration',
        ];

        foreach ($jobDisciplines as $name) {
            new CreateJobDiscipline(
                organization: $this->organization,
                jobFamily: $this->organization->jobFamilies()->inRandomOrder()->first(),
                user: $this->michael,
                jobDisciplineName: $name,
                description: null,
            )->execute();
        }
    }

    private function createJobLevels(): void
    {
        $jobLevels = [
            'IC3', 'IC4', 'IC5', 'IC6', 'IC7', 'IC8',
        ];

        foreach ($this->organization->jobDisciplines as $jobDiscipline) {
            foreach ($jobLevels as $name) {
                new CreateJobLevel(
                    organization: $this->organization,
                    jobDiscipline: $jobDiscipline,
                    user: $this->michael,
                    jobLevelName: $name,
                    description: null,
                )->execute();
            }
        }
    }
}
