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

    private function addJournal(): void
    {
        new CreateJournal(
            user: $this->michael,
            name: 'My first journal',
        )->execute();
    }
}
