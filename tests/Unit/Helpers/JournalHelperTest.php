<?php

declare(strict_types=1);

namespace Tests\Unit\Helpers;

use App\Helpers\JournalHelper;
use App\Models\Journal;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class JournalHelperTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_gets_all_months_in_a_given_year(): void
    {
        Carbon::setTestNow(Carbon::create(2023, 2, 1));
        $journal = Journal::factory()->create();

        $collection = JournalHelper::getMonths(
            journal: $journal,
            year: 2023,
            selectedMonth: 2,
        );

        $this->assertCount(12, $collection);
        $this->assertEquals((object) [
            'month' => 2,
            'month_name' => 'February',
            'entries_count' => 0,
            'is_selected' => true,
            'url' => env('APP_URL') . '/journals/' . $journal->slug . '/entries/2023/2/1',
        ], $collection[2]);
    }
}
