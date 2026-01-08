<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ModuleSexualActivity
 *
 * Represents sexual activity tracking data for a journal entry.
 *
 * @property int $id
 * @property int $journal_entry_id
 * @property string|null $had_sexual_activity # Format: 'yes'|'no'
 * @property string|null $sexual_activity_type # Format: 'solo', 'with-partner', 'intimate-contact'
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class ModuleSexualActivity extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleSexualActivityFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_sexual_activity';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_entry_id',
        'had_sexual_activity',
        'sexual_activity_type',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'had_sexual_activity' => 'encrypted',
            'sexual_activity_type' => 'encrypted',
        ];
    }

    /**
     * Get the journal entry that owns the sexual activity module.
     *
     * @return BelongsTo<JournalEntry, $this>
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
