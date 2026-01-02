<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Date;

/**
 * Class JournalEntry
 *
 * Most properties are encrypted to protect the privacy of the user.
 * This forces us to use strings to represent the values, even when it's not
 * the right data type.
 *
 * @property int $id
 * @property int $journal_id
 * @property int $day
 * @property int $month
 * @property int $year
 * @property bool $has_content
 * @property string|null $bedtime # Format: 'HH:MM'
 * @property string|null $wake_up_time # Format: 'HH:MM'
 * @property string|null $sleep_duration_in_minutes # Format: '12'
 * @property string|null $worked # Format: 'yes'|'no'
 * @property string|null $work_mode # Format: 'on-site'|'remote'|'hybrid'
 * @property string|null $work_load # Format: 'light'|'medium'|'heavy'
 * @property string|null $work_procrastinated # Format: 'yes'|'no'
 * @property string|null $has_traveled_today # Format: 'yes'|'no'
 * @property string|null $travel_details # Format: 'Took a flight to...'
 * @property array<string>|null $travel_mode # Format: ['car',plane,train,bike,bus,walk,boat,other]
 * @property string|null $day_type # Format: 'workday', 'day off', 'weekend', 'vacation', 'sick day'
 * @property string|null $has_done_physical_activity # Format: 'yes'|'no'
 * @property string|null $activity_type # Format: 'running', 'cycling', 'swimming', 'gym', 'walking'
 * @property string|null $activity_intensity # Format: 'light', 'moderate', 'intense'
 * @property string|null $health # Format: 'good', 'okay', 'not great'
 * @property string|null $mood # Format: 'terrible', 'bad', 'okay', 'good', 'great'
 * @property string|null $energy # Format: 'very low', 'low', 'normal', 'high', 'very high'
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class JournalEntry extends Model
{
    /** @use HasFactory<\Database\Factories\JournalEntryFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'journal_entries';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_id',
        'day',
        'month',
        'year',
        'has_content',
        'bedtime',
        'wake_up_time',
        'sleep_duration_in_minutes',
        'worked',
        'work_mode',
        'work_load',
        'work_procrastinated',
        'has_traveled_today',
        'travel_details',
        'travel_mode',
        'day_type',
        'has_done_physical_activity',
        'activity_type',
        'activity_intensity',
        'health',
        'mood',
        'energy',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'has_content' => 'boolean',
            'bedtime' => 'encrypted',
            'wake_up_time' => 'encrypted',
            'sleep_duration_in_minutes' => 'encrypted',
            'worked' => 'encrypted',
            'work_mode' => 'encrypted',
            'work_load' => 'encrypted',
            'work_procrastinated' => 'encrypted',
            'has_traveled_today' => 'encrypted',
            'travel_details' => 'encrypted',
            'travel_mode' => 'encrypted:array',
            'day_type' => 'encrypted',
            'has_done_physical_activity' => 'encrypted',
            'activity_type' => 'encrypted',
            'activity_intensity' => 'encrypted',
            'mood' => 'encrypted',
            'energy' => 'encrypted',
        ];
    }

    /**
     * Get the journal associated with the entry.
     *
     * @return BelongsTo<Journal, $this>
     */
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    /**
     * Get the date of the entry in a human readable format, like "2024/12/23".
     */
    public function getDate(): string
    {
        return Date::create($this->year, $this->month, $this->day)
            ->format('l F jS, Y');
    }
}
