<?php

declare(strict_types=1);

namespace Tests\Unit\ViewModels\Settings;

use App\Http\ViewModels\Settings\ProfileShowViewModel;
use App\Models\EmailSent;
use App\Models\Journal;
use App\Models\Log;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class ProfileShowViewModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_tells_the_user_if_they_have_more_logs(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Ross',
            'last_name' => 'Geller',
        ]);

        Log::factory()->count(6)->create([
            'user_id' => $user->id,
        ]);

        $viewModel = (new ProfileShowViewModel(
            user: $user,
        ));

        $this->assertTrue($viewModel->hasMoreLogs());
    }

    #[Test]
    public function it_gets_the_latest_logs(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));

        $user = User::factory()->create([
            'first_name' => 'Michael',
            'last_name' => 'Scott',
            'nickname' => null,
        ]);

        Log::factory()->create([
            'user_id' => $user->id,
            'action' => 'profile_update',
            'description' => 'Updated their profile',
        ]);

        $viewModel = (new ProfileShowViewModel(
            user: $user,
        ));

        $this->assertCount(1, $viewModel->logs());
        $this->assertEquals([
            'action' => 'profile_update',
            'journal_id' => null,
            'journal_name' => null,
            'description' => 'Updated their profile',
            'created_at' => '2018-01-01 00:00:00',
            'created_at_diff_for_humans' => '0 seconds ago',
        ], (array) $viewModel->logs()->first());
    }

    #[Test]
    public function it_gets_the_latest_logs_with_journal(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));

        $user = User::factory()->create([
            'first_name' => 'Michael',
            'last_name' => 'Scott',
            'nickname' => null,
        ]);

        $journal = Journal::factory()->create([
            'name' => 'Dunder Mifflin',
        ]);

        Log::factory()->create([
            'user_id' => $user->id,
            'action' => 'profile_update',
            'description' => 'Updated their profile',
            'journal_id' => $journal->id,
            'journal_name' => 'Dunder Mifflin',
        ]);

        $viewModel = (new ProfileShowViewModel(
            user: $user,
        ));

        $this->assertCount(1, $viewModel->logs());
        $this->assertEquals([
            'action' => 'profile_update',
            'journal_id' => $journal->id,
            'journal_name' => 'Dunder Mifflin',
            'description' => 'Updated their profile',
            'created_at' => '2018-01-01 00:00:00',
            'created_at_diff_for_humans' => '0 seconds ago',
        ], (array) $viewModel->logs()->first());
    }

    #[Test]
    public function it_gets_the_latest_emails_sent(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));

        $user = User::factory()->create([
            'first_name' => 'Michael',
            'last_name' => 'Scott',
            'nickname' => null,
        ]);

        EmailSent::factory()->create([
            'user_id' => $user->id,
            'email_type' => 'welcome',
            'email_address' => 'michael.scott@dundermifflin.com',
            'subject' => 'Welcome to our platform',
            'body' => 'Thank you for joining us!',
            'sent_at' => Carbon::now(),
            'delivered_at' => null,
            'bounced_at' => null,
        ]);

        $viewModel = (new ProfileShowViewModel(
            user: $user,
        ));

        $this->assertCount(1, $viewModel->emailsSent());
        $this->assertEquals([
            'email_type' => 'welcome',
            'email_address' => 'michael.scott@dundermifflin.com',
            'subject' => 'Welcome to our platform',
            'body' => 'Thank you for joining us!',
            'sent_at' => '0 seconds ago',
            'delivered_at' => null,
            'bounced_at' => null,
        ], (array) $viewModel->emailsSent()->first());
    }
}
