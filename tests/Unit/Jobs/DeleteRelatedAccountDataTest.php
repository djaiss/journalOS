<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\DeleteRelatedAccountData;
use App\Jobs\DeleteRelatedJournalData;
use App\Models\Book;
use App\Models\EmailSent;
use App\Models\Journal;
use App\Models\Log;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class DeleteRelatedAccountDataTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_dispatches_job_to_delete_journal_data(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $job = new DeleteRelatedAccountData($user->id);
        $job->handle();

        Queue::assertPushed(DeleteRelatedJournalData::class, function ($job) use ($journal) {
            return $job->journalId === $journal->id;
        });

        $this->assertDatabaseMissing('journals', [
            'id' => $journal->id,
        ]);
    }

    #[Test]
    public function it_deletes_all_journals_for_the_user(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal1 = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $journal2 = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        $job = new DeleteRelatedAccountData($user->id);
        $job->handle();

        Queue::assertPushed(DeleteRelatedJournalData::class, 2);

        $this->assertDatabaseMissing('journals', [
            'id' => $journal1->id,
        ]);
        $this->assertDatabaseMissing('journals', [
            'id' => $journal2->id,
        ]);
    }

    #[Test]
    public function it_deletes_user_related_data(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $emailSent = EmailSent::factory()->create([
            'user_id' => $user->id,
        ]);

        $job = new DeleteRelatedAccountData($user->id);
        $job->handle();

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
        $this->assertDatabaseMissing('emails_sent', [
            'id' => $emailSent->id,
        ]);
    }

    #[Test]
    public function it_deletes_logs_for_the_user(): void
    {
        $user = User::factory()->create();
        $log = Log::factory()->create([
            'user_id' => $user->id,
        ]);

        $job = new DeleteRelatedAccountData($user->id);
        $job->handle();

        $this->assertDatabaseMissing('logs', [
            'id' => $log->id,
        ]);
    }
}
