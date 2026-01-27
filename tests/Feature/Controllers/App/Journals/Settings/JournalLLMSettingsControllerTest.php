<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Settings;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class JournalLLMSettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_journal_llm_settings_page(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'name' => 'Dunder Mifflin',
        ]);

        $response = $this->actingAs($user)->get('/journals/' . $journal->slug . '/settings/llm');

        $response->assertOk();
        $response->assertSeeText('LLM access');
        $response->assertSeeText('Dunder Mifflin');
        $response->assertViewHas('journal', fn($viewJournal): bool => $viewJournal->id === $journal->id);
    }

    #[Test]
    public function it_returns_not_found_if_user_does_not_own_the_journal(): void
    {
        $user = User::factory()->create();
        $otherJournal = Journal::factory()->create();

        $response = $this->actingAs($user)->get('/journals/' . $otherJournal->slug . '/settings/llm');

        $response->assertNotFound();
    }

    #[Test]
    public function it_toggles_llm_access_from_false_to_true(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'has_llm_access' => false,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/llm', [
            'has_llm_access' => '1',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/llm');
        $response->assertSessionHas('status', __('Changes saved'));

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'has_llm_access' => true,
        ]);
    }

    #[Test]
    public function it_toggles_llm_access_from_true_to_false(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'has_llm_access' => true,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/llm', [
            'has_llm_access' => '0',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/llm');
        $response->assertSessionHas('status', __('Changes saved'));

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'has_llm_access' => false,
        ]);
    }

    #[Test]
    public function it_does_not_toggle_if_value_is_already_correct(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'has_llm_access' => true,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/llm', [
            'has_llm_access' => '1',
        ]);

        $response->assertRedirect('/journals/' . $journal->slug . '/settings/llm');
        $response->assertSessionHas('status', __('Changes saved'));

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'has_llm_access' => true,
        ]);

        Queue::assertNothingPushed();
    }

    #[Test]
    public function it_requires_has_llm_access_field(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/llm', []);

        $response->assertSessionHasErrors('has_llm_access');
    }

    #[Test]
    public function it_rejects_non_boolean_values(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/llm', [
            'has_llm_access' => 'invalid',
        ]);

        $response->assertSessionHasErrors('has_llm_access');
    }

    #[Test]
    public function it_rejects_string_yes(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/llm', [
            'has_llm_access' => 'yes',
        ]);

        $response->assertSessionHasErrors('has_llm_access');
    }

    #[Test]
    public function it_returns_not_found_if_user_does_not_own_the_journal_on_update(): void
    {
        $user = User::factory()->create();
        $otherJournal = Journal::factory()->create();

        $response = $this->actingAs($user)->put('/journals/' . $otherJournal->slug . '/settings/llm', [
            'has_llm_access' => true,
        ]);

        $response->assertNotFound();
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $journal = Journal::factory()->create();

        $response = $this->put('/journals/' . $journal->slug . '/settings/llm', [
            'has_llm_access' => true,
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

        $response = $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/llm', [
            'has_llm_access' => true,
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
            'has_llm_access' => false,
        ]);

        $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/llm', [
            'has_llm_access' => true,
        ]);

        Queue::assertPushed(LogUserAction::class);
    }

    #[Test]
    public function it_dispatches_update_user_last_activity_date_job_when_toggling(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'has_llm_access' => false,
        ]);

        $this->actingAs($user)->put('/journals/' . $journal->slug . '/settings/llm', [
            'has_llm_access' => true,
        ]);

        Queue::assertPushed(UpdateUserLastActivityDate::class);
    }
}
