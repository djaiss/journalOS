<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ModuleSleep
 *
 * Represents sleep tracking data for a journal entry.
 *
 * @property int $id
 * @property int $journal_entry_id
 * @property string $category # Format: string
 * @property string|null $bedtime # Format: 'HH:MM'
 * @property string|null $wake_up_time # Format: 'HH:MM'
 * @property string|null $sleep_duration_in_minutes # Format: '420'
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class ModuleSleep extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleSleepFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_sleep';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_entry_id',
        'category',
        'bedtime',
        'wake_up_time',
        'sleep_duration_in_minutes',
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
            'sleep_duration_in_minutes' => 'encrypted',
        ];
    }

    /**
     * Get the journal entry that owns the sleep module.
     *
     * @return BelongsTo<JournalEntry, $this>
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
