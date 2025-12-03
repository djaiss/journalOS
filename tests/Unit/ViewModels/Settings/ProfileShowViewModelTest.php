<?php

declare(strict_types=1);

use App\Http\ViewModels\Settings\ProfileShowViewModel;
use App\Models\EmailSent;
use App\Models\Log;
use App\Models\Organization;
use App\Models\User;
use Carbon\Carbon;

it('tells the user if they have more logs', function (): void {
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

    expect($viewModel->hasMoreLogs())->toBeTrue();
});

it('gets the latest logs', function (): void {
    Carbon::setTestNow(Carbon::create(2018, 1, 1));

    $user = User::factory()->create([
        'first_name' => 'Michael',
        'last_name' => 'Scott',
        'nickname' => null,
    ]);
    $organization = Organization::factory()->create([
        'name' => 'Dunder Mifflin',
    ]);

    Log::factory()->create([
        'user_id' => $user->id,
        'action' => 'profile_update',
        'description' => 'Updated their profile',
        'organization_id' => null,
    ]);

    $viewModel = (new ProfileShowViewModel(
        user: $user,
    ));

    expect($viewModel->logs())->toHaveCount(1);
    expect((array) $viewModel->logs()->first())->toEqual([
        'username' => 'Michael Scott',
        'action' => 'profile_update',
        'organization_id' => null,
        'organization_name' => null,
        'description' => 'Updated their profile',
        'created_at' => '2018-01-01 00:00:00',
        'created_at_diff_for_humans' => '0 seconds ago',
    ]);
});

it('gets the latest logs with organization', function (): void {
    Carbon::setTestNow(Carbon::create(2018, 1, 1));

    $user = User::factory()->create([
        'first_name' => 'Michael',
        'last_name' => 'Scott',
        'nickname' => null,
    ]);

    $organization = Organization::factory()->create([
        'name' => 'Dunder Mifflin',
    ]);

    Log::factory()->create([
        'user_id' => $user->id,
        'action' => 'profile_update',
        'description' => 'Updated their profile',
        'organization_id' => $organization->id,
    ]);

    $viewModel = (new ProfileShowViewModel(
        user: $user,
    ));

    expect($viewModel->logs())->toHaveCount(1);
    expect((array) $viewModel->logs()->first())->toEqual([
        'username' => 'Michael Scott',
        'action' => 'profile_update',
        'organization_id' => $organization->id,
        'organization_name' => 'Dunder Mifflin',
        'description' => 'Updated their profile',
        'created_at' => '2018-01-01 00:00:00',
        'created_at_diff_for_humans' => '0 seconds ago',
    ]);
});

it('gets the latest emails sent', function (): void {
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

    expect($viewModel->emailsSent())->toHaveCount(1);
    expect((array) $viewModel->emailsSent()->first())->toEqual([
        'email_type' => 'welcome',
        'email_address' => 'michael.scott@dundermifflin.com',
        'subject' => 'Welcome to our platform',
        'body' => 'Thank you for joining us!',
        'sent_at' => '0 seconds ago',
        'delivered_at' => null,
        'bounced_at' => null,
    ]);
});
