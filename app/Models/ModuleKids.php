<?php

declare(strict_types = 1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ModuleKids
 *
 * Represents kids tracking data for a journal entry.
 *
 * @property int $id
 * @property int $journal_entry_id
 * @property string $category # Format: string
 * @property string|null $had_kids_today # Format: 'yes'|'no'
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class ModuleKids extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleKidsFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_kids';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_entry_id',
        'category',
        'had_kids_today',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'had_kids_today' => 'encrypted',
        ];
    }

    /**
     * Get the journal entry that owns the kids module.
     *
     * @return BelongsTo<JournalEntry, $this>
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
