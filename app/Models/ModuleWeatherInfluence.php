<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ModuleWeatherInfluence
 *
 * Represents the influence of weather on a journal entry.
 *
 * @property int $id
 * @property int $journal_entry_id
 * @property string $category # Format: string
 * @property string|null $mood_effect # Format: 'positive', 'slight', 'none', 'negative'
 * @property string|null $energy_effect # Format: 'boosted', 'neutral', 'drained'
 * @property string|null $plans_influence # Format: 'none', 'slight', 'significant'
 * @property string|null $outside_time # Format: 'a_lot', 'some', 'barely', 'not_at_all'
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class ModuleWeatherInfluence extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleWeatherInfluenceFactory> */
    use HasFactory;

    public const array MOOD_EFFECTS = [
        'positive',
        'slight',
        'none',
        'negative',
    ];

    public const array ENERGY_EFFECTS = [
        'boosted',
        'neutral',
        'drained',
    ];

    public const array PLANS_INFLUENCES = [
        'none',
        'slight',
        'significant',
    ];

    public const array OUTSIDE_TIMES = [
        'a_lot',
        'some',
        'barely',
        'not_at_all',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_weather_influence';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_entry_id',
        'category',
        'mood_effect',
        'energy_effect',
        'plans_influence',
        'outside_time',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'mood_effect' => 'encrypted',
            'energy_effect' => 'encrypted',
            'plans_influence' => 'encrypted',
            'outside_time' => 'encrypted',
        ];
    }

    /**
     * Get the journal entry that owns the weather influence module.
     *
     * @return BelongsTo<JournalEntry, $this>
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
