<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ModuleTravel
 *
 * Represents travel tracking data for a journal entry.
 *
 * @property int $id
 * @property int $journal_entry_id
 * @property string|null $has_traveled_today # Format: 'yes'|'no'
 * @property string|null $travel_details # Format: 'Took a flight to...'
 * @property array<string>|null $travel_mode # Format: ['car',plane,train,bike,bus,walk,boat,other]
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class ModuleTravel extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleTravelFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_travel';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_entry_id',
        'has_traveled_today',
        'travel_details',
        'travel_mode',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'has_traveled_today' => 'encrypted',
            'travel_details' => 'encrypted',
            'travel_mode' => 'encrypted:array',
        ];
    }

    /**
     * Get the journal entry that owns the travel module.
     *
     * @return BelongsTo<JournalEntry, $this>
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
