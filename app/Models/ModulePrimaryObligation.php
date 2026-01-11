<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ModulePrimaryObligation
 *
 * Represents primary obligation data for a journal entry.
 *
 * @property int $id
 * @property int $journal_entry_id
 * @property string $category # Format: string
 * @property string|null $primary_obligation # Format: 'work', 'family', 'personal', 'health', 'travel', 'none'
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class ModulePrimaryObligation extends Model
{
    /** @use HasFactory<\Database\Factories\ModulePrimaryObligationFactory> */
    use HasFactory;

    public const array PRIMARY_OBLIGATIONS = [
        'work',
        'family',
        'personal',
        'health',
        'travel',
        'none',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_primary_obligation';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_entry_id',
        'category',
        'primary_obligation',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'primary_obligation' => 'encrypted',
        ];
    }

    /**
     * Get the journal entry that owns the primary obligation module.
     *
     * @return BelongsTo<JournalEntry, $this>
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
