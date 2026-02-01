<?php

declare(strict_types = 1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ModuleCognitiveLoad
 *
 * Represents cognitive load tracking data for a journal entry.
 *
 * @property int $id
 * @property int $journal_entry_id
 * @property string $category # Format: string
 * @property string|null $cognitive_load # Format: 'very low', 'low', 'high', 'overwhelming'
 * @property string|null $primary_source # Format: 'work', 'personal life', 'relationships', 'health', 'uncertainty', 'mixed'
 * @property string|null $load_quality # Format: 'productive', 'mixed', 'mostly wasteful'
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class ModuleCognitiveLoad extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleCognitiveLoadFactory> */
    use HasFactory;

    public const array COGNITIVE_LOAD_LEVELS = [
        'very low',
        'low',
        'high',
        'overwhelming',
    ];

    public const array PRIMARY_SOURCES = [
        'work',
        'personal life',
        'relationships',
        'health',
        'uncertainty',
        'mixed',
    ];

    public const array LOAD_QUALITIES = [
        'productive',
        'mixed',
        'mostly wasteful',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_cognitive_load';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_entry_id',
        'category',
        'cognitive_load',
        'primary_source',
        'load_quality',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cognitive_load' => 'encrypted',
            'primary_source' => 'encrypted',
            'load_quality' => 'encrypted',
        ];
    }

    /**
     * Get the journal entry that owns the cognitive load module.
     *
     * @return BelongsTo<JournalEntry, $this>
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
