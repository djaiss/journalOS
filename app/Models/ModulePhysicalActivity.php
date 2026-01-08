<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ModulePhysicalActivity
 *
 * Represents physical activity tracking data for a journal entry.
 *
 * @property int $id
 * @property int $journal_entry_id
 * @property string $category # Format: string
 * @property string|null $has_done_physical_activity # Format: 'yes'|'no'
 * @property string|null $activity_type # Format: 'running', 'cycling', 'swimming', 'gym', 'walking'
 * @property string|null $activity_intensity # Format: 'light', 'moderate', 'intense'
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class ModulePhysicalActivity extends Model
{
    /** @use HasFactory<\Database\Factories\ModulePhysicalActivityFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_physical_activity';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_entry_id',
        'category',
        'has_done_physical_activity',
        'activity_type',
        'activity_intensity',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'has_done_physical_activity' => 'encrypted',
            'activity_type' => 'encrypted',
            'activity_intensity' => 'encrypted',
        ];
    }

    /**
     * Get the journal entry that owns the physical activity module.
     *
     * @return BelongsTo<JournalEntry, $this>
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
