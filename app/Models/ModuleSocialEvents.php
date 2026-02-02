<?php

declare(strict_types = 1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ModuleSocialEvents
 *
 * Represents social events data for a journal entry.
 *
 * @property int $id
 * @property int $journal_entry_id
 * @property string $category # Format: string
 * @property string|null $event_type # Format: 'friends', 'family', 'work', 'networking', 'romantic', 'other'
 * @property string|null $tone # Format: 'positive', 'neutral', 'draining'
 * @property string|null $duration # Format: 'short', 'medium', 'long'
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class ModuleSocialEvents extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleSocialEventsFactory> */
    use HasFactory;

    public const array EVENT_TYPE_VALUES = [
        'friends',
        'family',
        'work',
        'networking',
        'romantic',
        'other',
    ];

    public const array TONE_VALUES = [
        'positive',
        'neutral',
        'draining',
    ];

    public const array DURATION_VALUES = [
        'short',
        'medium',
        'long',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_social_events';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_entry_id',
        'category',
        'event_type',
        'tone',
        'duration',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_type' => 'encrypted',
            'tone' => 'encrypted',
            'duration' => 'encrypted',
        ];
    }

    /**
     * Get the journal entry that owns the social events module.
     *
     * @return BelongsTo<JournalEntry, $this>
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
