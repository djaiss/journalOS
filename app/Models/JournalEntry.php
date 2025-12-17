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
 * @property int $id
 * @property int $journal_id
 * @property int $day
 * @property int $month
 * @property int $year
 * @property string|null $bedtime
 * @property string|null $wake_up_time
 * @property string|null $sleep_duration
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
        'bedtime',
        'wake_up_time',
        'sleep_duration',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'bedtime' => 'encrypted',
            'wake_up_time' => 'encrypted',
            'sleep_duration' => 'encrypted',
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
     *
     * @return string
     */
    public function getDate(): string
    {
        return Date::create($this->year, $this->month, $this->day)
            ->format('l F jS, Y');
    }
}
