<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class JournalEntry
 *
 * @property int $id
 * @property int $journal_id
 * @property int $day
 * @property int $month
 * @property int $year
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
    ];

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
        return Carbon::create($this->year, $this->month, $this->day)
            ->format('l F jS, Y');
    }
}
