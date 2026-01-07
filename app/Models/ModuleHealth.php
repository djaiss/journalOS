<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ModuleHealth
 *
 * Represents health tracking data for a journal entry.
 *
 * @property int $id
 * @property int $journal_entry_id
 * @property string|null $health # Format: 'good', 'okay', 'not great'
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class ModuleHealth extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleHealthFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_health';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_entry_id',
        'health',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'health' => 'encrypted',
        ];
    }

    /**
     * Get the journal entry that owns the health module.
     *
     * @return BelongsTo<JournalEntry, $this>
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
