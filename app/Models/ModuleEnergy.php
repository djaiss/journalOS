<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ModuleEnergy
 *
 * Represents energy tracking data for a journal entry.
 *
 * @property int $id
 * @property int $journal_entry_id
 * @property string $category # Format: string
 * @property string|null $energy # Format: 'very low', 'low', 'normal', 'high', 'very high'
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class ModuleEnergy extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleEnergyFactory> */
    use HasFactory;

    public const array ENERGY_LEVELS = [
        'very low',
        'low',
        'normal',
        'high',
        'very high',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_energy';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_entry_id',
        'category',
        'energy',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'energy' => 'encrypted',
        ];
    }

    /**
     * Get the journal entry that owns the energy module.
     *
     * @return BelongsTo<JournalEntry, $this>
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
