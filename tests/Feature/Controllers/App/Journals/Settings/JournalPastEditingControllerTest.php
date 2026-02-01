<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App\Journals\Settings;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class JournalPastEditingControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_toggles_can_edit_past_from_false_to_true(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'can_edit_past' => false,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/edit-past', [
            'can_edit_past' => '1',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/management');
        $response->assertSessionHas('status', __('Changes saved'));

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'can_edit_past' => true,
        ]);
    }

    #[Test]
    public function it_toggles_can_edit_past_from_true_to_false(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'can_edit_past' => true,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/edit-past', [
            'can_edit_past' => '0',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/management');
        $response->assertSessionHas('status', __('Changes saved'));

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'can_edit_past' => false,
        ]);
    }

    #[Test]
    public function it_does_not_toggle_if_value_is_already_correct(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'can_edit_past' => true,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/edit-past', [
            'can_edit_past' => '1',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/management');
        $response->assertSessionHas('status', __('Changes saved'));

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'can_edit_past' => true,
        ]);

        // no jobs should have been dispatched since we didn't toggle
        Queue::assertNothingPushed();
    }

    #[Test]
    public function it_accepts_boolean_true_value(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'can_edit_past' => false,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/edit-past', [
            'can_edit_past' => true,
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/management');

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'can_edit_past' => true,
        ]);
    }

    #[Test]
    public function it_accepts_boolean_false_value(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'can_edit_past' => true,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/edit-past', [
            'can_edit_past' => false,
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/management');

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'can_edit_past' => false,
        ]);
    }

    #[Test]
    public function it_requires_can_edit_past_field(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/edit-past', []);

        $response->assertSessionHasErrors('can_edit_past');
    }

    #[Test]
    public function it_rejects_non_boolean_values(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/edit-past', [
            'can_edit_past' => 'invalid',
        ]);

        $response->assertSessionHasErrors('can_edit_past');
    }

    #[Test]
    public function it_rejects_string_yes(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/edit-past', [
            'can_edit_past' => 'yes',
        ]);

        $response->assertSessionHasErrors('can_edit_past');
    }

    #[Test]
    public function it_rejects_string_no(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/edit-past', [
            'can_edit_past' => 'no',
        ]);

        $response->assertSessionHasErrors('can_edit_past');
    }

    #[Test]
    public function it_rejects_null_value(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/edit-past', [
            'can_edit_past' => null,
        ]);

        $response->assertSessionHasErrors('can_edit_past');
    }

    #[Test]
    public function it_rejects_numeric_2(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/edit-past', [
            'can_edit_past' => 2,
        ]);

        $response->assertSessionHasErrors('can_edit_past');
    }

    #[Test]
    public function it_returns_not_found_if_user_does_not_own_the_journal(): void
    {
        $user = User::factory()->create();
        $otherJournal = Journal::factory()->create();

        $response = $this->actingAs($user)->put('/journals/' . $otherJournal->slug . '/settings/edit-past', [
            'can_edit_past' => true,
        ]);

        $response->assertNotFound();
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $journal = Journal::factory()->create();

        $response = $this->put('/journals/' . $journal->slug . '/settings/edit-past', [
            'can_edit_past' => true,
        ]);

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_requires_verified_email(): void
    {
        $user = User::factory()->unverified()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/edit-past', [
            'can_edit_past' => true,
        ]);

        $response->assertRedirect('/verify-email');
    }

    #[Test]
    public function it_dispatches_log_user_action_job_when_toggling(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'can_edit_past' => false,
        ]);

        $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/edit-past', [
            'can_edit_past' => true,
        ]);

        Queue::assertPushed(\App\Jobs\LogUserAction::class);
    }

    #[Test]
    public function it_dispatches_update_user_last_activity_date_job_when_toggling(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'can_edit_past' => false,
        ]);

        $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/edit-past', [
            'can_edit_past' => true,
        ]);

        Queue::assertPushed(\App\Jobs\UpdateUserLastActivityDate::class);
    }

    #[Test]
    public function it_correctly_handles_form_submission_with_string_1(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'can_edit_past' => false,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/edit-past', [
            'can_edit_past' => '1',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/management');

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'can_edit_past' => true,
        ]);
    }

    #[Test]
    public function it_correctly_handles_form_submission_with_string_0(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'can_edit_past' => true,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/edit-past', [
            'can_edit_past' => '0',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/management');

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'can_edit_past' => false,
        ]);
    }
}
