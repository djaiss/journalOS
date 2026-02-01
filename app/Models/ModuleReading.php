<?php

declare(strict_types = 1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ModuleReading
 *
 * Represents reading tracking data for a journal entry.
 *
 * @property int $id
 * @property int $journal_entry_id
 * @property string $category # Format: string
 * @property string|null $did_read_today # Format: 'yes'|'no'
 * @property string|null $reading_amount # Format: 'a few pages'|'one solid session'|'multiple sessions'|'deep immersion'
 * @property string|null $mental_state # Format: 'stimulated'|'calm'|'neutral'|'overloaded'
 * @property string|null $reading_feel # Format: 'effortless'|'engaging'|'demanding'|'hard to focus'
 * @property string|null $want_continue # Format: 'strongly'|'somewhat'|'not really'
 * @property string|null $reading_limit # Format: 'time'|'energy'|'distraction'|'nothing'
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class ModuleReading extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleReadingFactory> */
    use HasFactory;

    public const array READING_AMOUNTS = [
        'a few pages',
        'one solid session',
        'multiple sessions',
        'deep immersion',
    ];

    public const array MENTAL_STATES = [
        'stimulated',
        'calm',
        'neutral',
        'overloaded',
    ];

    public const array READING_FEELS = [
        'effortless',
        'engaging',
        'demanding',
        'hard to focus',
    ];

    public const array WANT_CONTINUE_OPTIONS = [
        'strongly',
        'somewhat',
        'not really',
    ];

    public const array READING_LIMITS = [
        'time',
        'energy',
        'distraction',
        'nothing',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_reading';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_entry_id',
        'category',
        'did_read_today',
        'reading_amount',
        'mental_state',
        'reading_feel',
        'want_continue',
        'reading_limit',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'did_read_today' => 'encrypted',
            'reading_amount' => 'encrypted',
            'mental_state' => 'encrypted',
            'reading_feel' => 'encrypted',
            'want_continue' => 'encrypted',
            'reading_limit' => 'encrypted',
        ];
    }

    /**
     * Get the journal entry that owns the reading module.
     *
     * @return BelongsTo<JournalEntry, $this>
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
