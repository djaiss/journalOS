<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\CreateAccount;
use App\Actions\CreateJournal;
use App\Models\User;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    private User $michael;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->createDunderMifflin();
        $this->validateEmail();
        $this->addJournal();
        $this->addEntries();
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
        $this->michael->is_instance_admin = true;
        $this->michael->trial_ends_at = now()->addDays(14);
        $this->michael->save();
    }

    private function addJournal(): void
    {
        new CreateJournal(
            user: $this->michael,
            name: 'My first journal',
        )->execute();
    }

    private function addEntries(): void
    {
        $journal = $this->michael->journals()->first();

        $currentYear = now()->year;
        $numberOfYears = random_int(3, 6);

        // Get past years (excluding current year)
        $pastYears = range($currentYear - 10, $currentYear - 1);
        shuffle($pastYears);

        // Select random past years (max numberOfYears - 1, to leave room for current year)
        $selectedYears = array_slice($pastYears, 0, $numberOfYears - 1);

        // Always include current year
        $selectedYears[] = $currentYear;

        // Create one entry for each year
        foreach ($selectedYears as $year) {
            $journal->entries()->create([
                'year' => $year,
                'month' => random_int(1, 12),
                'day' => random_int(1, 28),
            ]);
        }
    }
}
