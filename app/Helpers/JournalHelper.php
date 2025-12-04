<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Journal;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * This class is responsible for providing helper methods related to the journal.
 */
final class JournalHelper
{
    /**
     * Get all the months in a given year, with the current month marked as
     * selected if it is the current month.
     * For each month, get the number of non-empty entries in that month.
     * Returns the following structure:
     * [
     *    1 => [
     *      'month' => 1,
     *      'month_name' => 'January',
     *      'entries_count' => 5,
     *      'is_selected' => false,
     *      'url' => '/journal/journal-slug/2023/01',
     *   ]
     *
     * @param Journal $journal The journal to get the months for.
     * @param int $year The year to get the months for.
     * @param int $selectedMonth The month to mark as selected.
     *
     * @return Collection The months in the year.
     */
    public static function getMonths(Journal $journal, int $year, int $selectedMonth): Collection
    {
        return collect(range(1, 12))->mapWithKeys(fn(int $month): array => [
            $month => (object) [
                'month' => $month,
                'month_name' => ucfirst(Carbon::createFromDate($year, $month, 1)->translatedFormat('F')),
                'entries_count' => 0,
                'is_selected' => $month === $selectedMonth,
                'url' => route('journal.entry.show', [
                    'slug' => $journal->slug,
                    'year' => $year,
                    'month' => $month,
                    'day' => 1,
                ]),
            ],
        ]);
    }

    /**
     * Get the days in a given month of the given year.
     * Returns the following structure:
     * [
     *    1 => [
     *      'day' => 1,
     *      'is_today' => false,
     *      'is_selected' => false,
     *      'has_blocks' => true,
     *      'url' => '/journal/2023/01/01',
     *   ]
     *
     * @param Journal $journal The journal to get the days for.
     * @param int $year The year to get the days for.
     * @param int $month The month to get the days for.
     * @param int $day The day to mark as selected.
     *
     * @return Collection The days in the month.
     */
    public static function getDaysInMonth(Journal $journal, int $year, int $month, int $day): Collection
    {
        return collect(range(1, cal_days_in_month(CAL_GREGORIAN, $month, $year)))
            ->mapWithKeys(fn(int $currentDay): array => [
                $currentDay => (object) [
                    'day' => $currentDay,
                    'is_today' => Carbon::createFromDate($year, $month, $currentDay)->isToday(),
                    'is_selected' => $currentDay === $day,
                    'has_blocks' => 0,
                    'url' => route('journal.entry.show', [
                        'slug' => $journal->slug,
                        'year' => $year,
                        'month' => $month,
                        'day' => $currentDay,
                    ]),
                ],
            ]);
    }
}
