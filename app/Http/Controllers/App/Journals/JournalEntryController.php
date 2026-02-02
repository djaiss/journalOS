<?php

declare(strict_types = 1);

namespace App\Http\Controllers\App\Journals;

use App\Helpers\JournalHelper;
use App\Http\Controllers\Controller;
use App\View\Presenters\JournalEntryPresenter;
use App\View\Presenters\JournalEntryShowPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class JournalEntryController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $journal = $request->attributes->get('journal');
        $journalEntry = $request->attributes->get('journal_entry');

        if ($journalEntry->is_edited) {
            return to_route('journal.entry.edit', [
                'slug' => $journalEntry->journal->slug,
                'year' => $journalEntry->year,
                'month' => $journalEntry->month,
                'day' => $journalEntry->day,
            ]);
        }

        $years = JournalHelper::getYears(
            journal: $journal,
            selectedYear: $journalEntry->year,
        );

        $months = JournalHelper::getMonths(
            journal: $journal,
            year: $journalEntry->year,
            selectedMonth: $journalEntry->month,
        );

        $days = JournalHelper::getDaysInMonth(
            journal: $journal,
            year: $journalEntry->year,
            month: $journalEntry->month,
            day: $journalEntry->day,
        );

        $payload = new JournalEntryShowPresenter($journalEntry)->build();

        return view('app.journal.entry.show', [
            'journal' => $journal,
            'entry' => $journalEntry,
            'years' => $years,
            'months' => $months,
            'days' => $days,
            'entryDate' => $payload['date'],
            'notesMarkdown' => $payload['notes_markdown'],
            'modules' => $payload['modules'],
        ]);
    }

    public function edit(Request $request): View
    {
        $journal = $request->attributes->get('journal');
        $journalEntry = $request->attributes->get('journal_entry');

        $years = JournalHelper::getYears(
            journal: $journal,
            selectedYear: $journalEntry->year,
        );

        $months = JournalHelper::getMonths(
            journal: $journal,
            year: $journalEntry->year,
            selectedMonth: $journalEntry->month,
        );

        $days = JournalHelper::getDaysInMonth(
            journal: $journal,
            year: $journalEntry->year,
            month: $journalEntry->month,
            day: $journalEntry->day,
        );

        $payload = new JournalEntryPresenter($journalEntry)->build();

        return view('app.journal.entry.edit', [
            'journal' => $journal,
            'entry' => $journalEntry,
            'years' => $years,
            'months' => $months,
            'days' => $days,
            'columns' => $payload['columns'],
            'notes' => $payload['notes'],
            'layoutColumnsCount' => $payload['layout_columns_count'],
        ]);
    }

    public function report(Request $request): View
    {
        $journal = $request->attributes->get('journal');
        $journalEntry = $request->attributes->get('journal_entry');

        $years = JournalHelper::getYears(
            journal: $journal,
            selectedYear: $journalEntry->year,
        );

        $months = JournalHelper::getMonths(
            journal: $journal,
            year: $journalEntry->year,
            selectedMonth: $journalEntry->month,
        );

        $days = JournalHelper::getDaysInMonth(
            journal: $journal,
            year: $journalEntry->year,
            month: $journalEntry->month,
            day: $journalEntry->day,
        );

        $lifeModules = [];
        $dayModules = [];
        $leisureModules = [];

        if ($journal->show_sleep_module) {
            $lifeModules[] = [
                'title' => __('Sleep tracking'),
                'emoji' => '🌖',
                'summary' => __('8h 15m of rest'),
                'items' => [
                    ['question' => __('Time you went to sleep'), 'answer' => '22:45'],
                    ['question' => __('Time you woke up'), 'answer' => '07:00'],
                ],
            ];
        }

        if ($journal->show_travel_module) {
            $lifeModules[] = [
                'title' => __('Travel'),
                'emoji' => '✈️',
                'summary' => __('Yes, a light commute'),
                'items' => [
                    ['question' => __('Have you traveled today?'), 'answer' => __('Yes')],
                    ['question' => __('How did you travel?'), 'answer' => __('Train, Walking')],
                ],
            ];
        }

        if ($journal->show_shopping_module) {
            $lifeModules[] = [
                'title' => __('Shopping'),
                'emoji' => '🛍️',
                'summary' => __('Essentials and a small treat'),
                'items' => [
                    ['question' => __('Shopped today?'), 'answer' => __('Yes')],
                    ['question' => __('Shopping type'), 'answer' => __('Groceries, Personal')],
                    ['question' => __('Intent'), 'answer' => __('Planned')],
                    ['question' => __('Shopping context'), 'answer' => __('In store, On the way home')],
                    ['question' => __('Shopping for'), 'answer' => __('Self, Home')],
                ],
            ];
        }

        if ($journal->show_kids_module) {
            $lifeModules[] = [
                'title' => __('Kids today'),
                'emoji' => '🧒',
                'summary' => __('Afternoon together'),
                'items' => [
                    ['question' => __('Did you have the kids today?'), 'answer' => __('Yes')],
                ],
            ];
        }

        if ($journal->show_day_type_module) {
            $dayModules[] = [
                'title' => __('Day type'),
                'emoji' => '📅',
                'summary' => __('Focused and steady'),
                'items' => [
                    ['question' => __('What type of day was it?'), 'answer' => __('Productive')],
                ],
            ];
        }

        if ($journal->show_primary_obligation_module) {
            $dayModules[] = [
                'title' => __('Primary obligation'),
                'emoji' => '🎯',
                'summary' => __('Top priority today'),
                'items' => [
                    ['question' => __('What demanded most of your attention today?'), 'answer' => __('Client project delivery')],
                ],
            ];
        }

        if ($journal->show_work_module) {
            $dayModules[] = [
                'title' => __('Work'),
                'emoji' => '💼',
                'summary' => __('Deep work with a few meetings'),
                'items' => [
                    ['question' => __('Have you worked today?'), 'answer' => __('Yes')],
                    ['question' => __('How did you work?'), 'answer' => __('Focus blocks, Meetings')],
                    ['question' => __('How much did you work?'), 'answer' => __('6-8 hours')],
                    ['question' => __('Did you procrastinate (be honest)?'), 'answer' => __('A little')],
                ],
            ];
        }

        if ($journal->show_health_module) {
            $dayModules[] = [
                'title' => __('Health'),
                'emoji' => '❤️',
                'summary' => __('Balanced with a mild headache'),
                'items' => [
                    ['question' => __('How did you feel today?'), 'answer' => __('Good overall')],
                ],
            ];
        }

        if ($journal->show_hygiene_module) {
            $dayModules[] = [
                'title' => __('Hygiene'),
                'emoji' => '🧼',
                'summary' => __('Full routine'),
                'items' => [
                    ['question' => __('Showered'), 'answer' => __('Yes')],
                    ['question' => __('Brushed teeth'), 'answer' => __('Yes')],
                    ['question' => __('Skincare'), 'answer' => __('Yes')],
                ],
            ];
        }

        if ($journal->show_mood_module) {
            $dayModules[] = [
                'title' => __('Mood'),
                'emoji' => '🙂',
                'summary' => __('Calm and optimistic'),
                'items' => [
                    ['question' => __('How was your mood today?'), 'answer' => __('Positive')],
                ],
            ];
        }

        if ($journal->show_energy_module) {
            $dayModules[] = [
                'title' => __('Energy'),
                'emoji' => '⚡️',
                'summary' => __('Steady, peaking mid-afternoon'),
                'items' => [
                    ['question' => __('How was your energy today?'), 'answer' => __('High')],
                ],
            ];
        }

        if ($journal->show_social_density_module) {
            $dayModules[] = [
                'title' => __('Social density'),
                'emoji' => '👥',
                'summary' => __('A handful of touchpoints'),
                'items' => [
                    ['question' => __('How was your social density today?'), 'answer' => __('Moderate')],
                ],
            ];
        }

        if ($journal->show_physical_activity_module) {
            $leisureModules[] = [
                'title' => __('Physical Activity'),
                'emoji' => '🏃‍♂️',
                'summary' => __('Quick session before dinner'),
                'items' => [
                    ['question' => __('Did you do physical activity?'), 'answer' => __('Yes')],
                    ['question' => __('What type of activity?'), 'answer' => __('Run, Mobility')],
                    ['question' => __('How intense was it?'), 'answer' => __('Moderate')],
                ],
            ];
        }

        if ($journal->show_sexual_activity_module) {
            $leisureModules[] = [
                'title' => __('Sexual activity'),
                'emoji' => '❤️',
                'summary' => __('Connection and intimacy'),
                'items' => [
                    ['question' => __('Did you have sexual activity?'), 'answer' => __('Yes')],
                    ['question' => __('What kind of sexual activity?'), 'answer' => __('Affection, Intimacy')],
                ],
            ];
        }

        $reportSections = [
            [
                'title' => __('Life'),
                'description' => __('What happened today'),
                'modules' => $lifeModules,
            ],
            [
                'title' => __('Day'),
                'description' => __('What shaped the day'),
                'modules' => $dayModules,
            ],
            [
                'title' => __('Leisure'),
                'description' => __('What you did for yourself'),
                'modules' => $leisureModules,
            ],
        ];

        return view('app.journal.entry.report', [
            'journal' => $journal,
            'entry' => $journalEntry,
            'years' => $years,
            'months' => $months,
            'days' => $days,
            'reportSections' => $reportSections,
        ]);
    }
}
