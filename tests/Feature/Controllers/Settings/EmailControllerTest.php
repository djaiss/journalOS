<?php

declare(strict_types=1);

use App\Models\EmailSent;
use App\Models\User;
use Carbon\Carbon;

it('shows all the emails', function (): void {
    Carbon::setTestNow(Carbon::create(2018, 1, 1));
    $user = User::factory()->create([
        'first_name' => 'Ross',
        'last_name' => 'Geller',
        'nickname' => null,
    ]);

    $email = EmailSent::factory()->create([
        'user_id' => $user->id,
        'subject' => 'Test Subject',
        'body' => 'Test Body',
        'sent_at' => Carbon::now(),
    ]);

    $response = $this->actingAs($user)
        ->get('/settings/profile/emails');

    $response->assertStatus(200);
    $response->assertViewIs('settings.emails.index');
    $response->assertViewHas('emails');

    $emails = $response->viewData('emails');
    expect($emails)->toHaveCount(1);
    expect($emails[0]->id)->toEqual($email->id);
    expect($emails[0]->user->getFullName())->toEqual('Ross Geller');
    expect($emails[0]->subject)->toEqual('Test Subject');
    expect($emails[0]->body)->toEqual('Test Body');
    expect($emails[0]->sent_at)->toEqual(Carbon::now());
});
