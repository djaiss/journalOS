<?php

declare(strict_types=1);

namespace Tests\Unit\ViewModels\Journal;

use App\Http\ViewModels\Journal\ModuleSleepViewModel;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ModuleSleepViewModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_builds_sleep_options_and_navigation_links(): void
    {
        $user = User::factory()->create([
            'time_format_24h' => true,
        ]);
        Auth::shouldReceive('user')->andReturn($user);

        $journal = Journal::factory()->for($user)->create([
            'name' => 'RestJournal',
        ]);

        $journalEntry = JournalEntry::factory()->for($journal)->create([
            'day' => 15,
            'month' => 12,
            'year' => 2025,
            'bedtime' => '22:00',
            'wake_up_time' => '06:00',
        ]);

        $viewModel = new ModuleSleepViewModel(
            journalEntry: $journalEntry,
        );

        $result = $viewModel->sleep('20:00', '05:00');

        $this->assertEquals([
            ['time' => '20:00', 'formatted' => '20:00', 'is_selected' => false],
            ['time' => '21:00', 'formatted' => '21:00', 'is_selected' => false],
            ['time' => '22:00', 'formatted' => '22:00', 'is_selected' => true],
            ['time' => '23:00', 'formatted' => '23:00', 'is_selected' => false],
            ['time' => '00:00', 'formatted' => '00:00', 'is_selected' => false],
        ], $result['bedtime']->all());

        $this->assertEquals([
            ['time' => '05:00', 'formatted' => '05:00', 'is_selected' => false],
            ['time' => '06:00', 'formatted' => '06:00', 'is_selected' => true],
            ['time' => '07:00', 'formatted' => '07:00', 'is_selected' => false],
            ['time' => '08:00', 'formatted' => '08:00', 'is_selected' => false],
            ['time' => '09:00', 'formatted' => '09:00', 'is_selected' => false],
        ], $result['wake_up_time']->all());

        $this->assertSame(
            "/journals/{$journal->slug}/entries/{$journalEntry->year}/{$journalEntry->month}/{$journalEntry->day}/sleep/01:00/05:00",
            parse_url($result['next_bedtime_url'], PHP_URL_PATH),
        );

        $this->assertSame(
            "/journals/{$journal->slug}/entries/{$journalEntry->year}/{$journalEntry->month}/{$journalEntry->day}/sleep/15:00/05:00",
            parse_url($result['previous_bedtime_url'], PHP_URL_PATH),
        );

        $this->assertSame(
            "/journals/{$journal->slug}/entries/{$journalEntry->year}/{$journalEntry->month}/{$journalEntry->day}/sleep/20:00/10:00",
            parse_url($result['next_wake_up_time_url'], PHP_URL_PATH),
        );

        $this->assertSame(
            "/journals/{$journal->slug}/entries/{$journalEntry->year}/{$journalEntry->month}/{$journalEntry->day}/sleep/20:00/00:00",
            parse_url($result['previous_wake_up_time_url'], PHP_URL_PATH),
        );
    }
}
