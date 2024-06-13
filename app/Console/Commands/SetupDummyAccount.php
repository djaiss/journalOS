<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\CreateAccount;
use Faker\Factory as Faker;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class SetupDummyAccount extends Command
{
    use ConfirmableTrait;

    protected ?\Faker\Generator $faker;

    protected User $user;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'organizationOS:dummy
                            {--migrate : Use migrate command instead of migrate:fresh.}
                            {--force : Force the operation to run.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepare an account with fake data so users can play with it';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // remove queue
        config(['queue.default' => 'sync']);

        $this->start();
        $this->wipeAndMigrateDB();
        $this->clearCache();
        $this->createFirstUser();
        $this->createAnotherAccount();

        $this->stop();
    }

    private function start(): void
    {
        if (! $this->confirmToProceed('Are you sure you want to proceed? This will delete ALL data in your environment.', true)) {
            exit;
        }

        $this->line('This process will take a few minutes to complete. Be patient and read a book in the meantime.');
        $this->faker = Faker::create();
    }

    private function wipeAndMigrateDB(): void
    {
        if ($this->option('migrate')) {
            $this->artisan('☐ Migration of the database', 'migrate', ['--force' => true]);
        } else {
            $this->artisan('☐ Migration of the database', 'migrate:fresh', ['--force' => true]);
        }
    }

    private function clearCache(): void
    {
        $this->artisan('☐ Clear cache', 'cache:clear');
    }

    private function stop(): void
    {
        $this->line('');
        $this->line('-----------------------------');
        $this->line('|');
        $this->line('| Welcome to OrganizationOS');
        $this->line('|');
        $this->line('-----------------------------');
        $this->info('| You can now sign in with one of these two accounts:');
        $this->line('| An account with a lot of data:');
        $this->line('| username: admin@admin.com');
        $this->line('| password: admin123');
        $this->line('|------------------------–––-');
        $this->line('|A blank account:');
        $this->line('| username: blank@blank.com');
        $this->line('| password: blank123');
        $this->line('|------------------------–––-');
        $this->line('| URL:      '.config('app.url'));
        $this->line('-----------------------------');

        $this->info('Setup is done. Have fun.');
    }

    private function createFirstUser(): void
    {
        $this->info('☐ Create first user of the account');

        $this->user = (new CreateAccount(
            email: 'admin@admin.com',
            password: 'admin123',
            name: 'Michael',
        ))->execute();
        $this->user->email_verified_at = Carbon::now();
        $this->user->save();

        Auth::login($this->user);
    }

    private function createAnotherAccount(): void
    {
        $this->info('☐ Create another account');

        $this->user = (new CreateAccount(
            email: 'blank@blank.com',
            password: 'blank123',
            name: 'Dwight',
        ))->execute();
        $this->user->email_verified_at = Carbon::now();
        $this->user->save();
    }

    private function artisan(string $message, string $command, array $arguments = []): void
    {
        $this->info($message);
        $this->callSilent($command, $arguments);
    }
}
