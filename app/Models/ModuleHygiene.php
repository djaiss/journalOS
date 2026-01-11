<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ModuleHygiene
 *
 * Represents hygiene tracking data for a journal entry.
 *
 * @property int $id
 * @property int $journal_entry_id
 * @property string $category # Format: string
 * @property string|null $showered # Format: 'yes', 'no'
 * @property string|null $brushed_teeth # Format: 'no', 'am', 'pm'
 * @property string|null $skincare # Format: 'yes', 'no'
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class ModuleHygiene extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleHygieneFactory> */
    use HasFactory;

    public const array BRUSHED_TEETH_VALUES = [
        'no',
        'am',
        'pm',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_hygiene';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_entry_id',
        'category',
        'showered',
        'brushed_teeth',
        'skincare',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'showered' => 'encrypted',
            'brushed_teeth' => 'encrypted',
            'skincare' => 'encrypted',
        ];
    }

    /**
     * Get the journal entry that owns the hygiene module.
     *
     * @return BelongsTo<JournalEntry, $this>
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
