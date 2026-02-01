<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App\Settings;

use App\Models\EmailSent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class EmailControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_all_the_emails(): void
    {
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
        $response->assertViewIs('app.settings.emails.index');
        $response->assertViewHas('emails');

        $emails = $response->viewData('emails');
        $this->assertCount(1, $emails);
        $this->assertEquals($email->id, $emails[0]->id);
        $this->assertEquals('Ross Geller', $emails[0]->user->getFullName());
        $this->assertEquals('Test Subject', $emails[0]->subject);
        $this->assertEquals('Test Body', $emails[0]->body);
        $this->assertEquals(Carbon::now(), $emails[0]->sent_at);
    }
}
