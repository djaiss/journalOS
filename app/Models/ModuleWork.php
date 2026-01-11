<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ModuleWork
 *
 * Represents work tracking data for a journal entry.
 *
 * @property int $id
 * @property int $journal_entry_id
 * @property string $category # Format: string
 * @property string|null $worked # Format: 'yes'|'no'
 * @property string|null $work_mode # Format: 'on-site'|'remote'|'hybrid'
 * @property string|null $work_load # Format: 'light'|'medium'|'heavy'
 * @property string|null $work_procrastinated # Format: 'yes'|'no'
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class ModuleWork extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleWorkFactory> */
    use HasFactory;

    public const array WORK_MODES = [
        'remote',
        'on-site',
        'hybrid',
    ];

    public const array WORK_LOADS = [
        'light',
        'medium',
        'heavy',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_work';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_entry_id',
        'category',
        'worked',
        'work_mode',
        'work_load',
        'work_procrastinated',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'worked' => 'encrypted',
            'work_mode' => 'encrypted',
            'work_load' => 'encrypted',
            'work_procrastinated' => 'encrypted',
        ];
    }

    /**
     * Get the journal entry that owns the work module.
     *
     * @return BelongsTo<JournalEntry, $this>
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
