<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ModuleDayType
 *
 * Represents day type tracking data for a journal entry.
 *
 * @property int $id
 * @property int $journal_entry_id
 * @property string|null $day_type # Format: 'workday', 'day off', 'weekend', 'vacation', 'sick day'
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class ModuleDayType extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleDayTypeFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_day_type';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_entry_id',
        'day_type',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'day_type' => 'encrypted',
        ];
    }

    /**
     * Get the journal entry that owns the day type module.
     *
     * @return BelongsTo<JournalEntry, $this>
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
