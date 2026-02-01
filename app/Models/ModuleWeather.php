<?php

declare(strict_types = 1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ModuleWeather
 *
 * Represents weather tracking data for a journal entry.
 *
 * @property int $id
 * @property int $journal_entry_id
 * @property string $category # Format: string
 * @property string|null $condition # Format: 'sunny', 'cloudy', 'rain', 'snow', 'mixed'
 * @property string|null $temperature_range # Format: 'very_cold', 'cold', 'mild', 'warm', 'hot'
 * @property string|null $precipitation # Format: 'none', 'light', 'heavy'
 * @property string|null $daylight # Format: 'very_short', 'normal', 'very_long'
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class ModuleWeather extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleWeatherFactory> */
    use HasFactory;

    public const array CONDITIONS = [
        'sunny',
        'cloudy',
        'rain',
        'snow',
        'mixed',
    ];

    public const array TEMPERATURE_RANGES = [
        'very_cold',
        'cold',
        'mild',
        'warm',
        'hot',
    ];

    public const array PRECIPITATION_LEVELS = [
        'none',
        'light',
        'heavy',
    ];

    public const array DAYLIGHT_VALUES = [
        'very_short',
        'normal',
        'very_long',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_weather';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_entry_id',
        'category',
        'condition',
        'temperature_range',
        'precipitation',
        'daylight',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'condition' => 'encrypted',
            'temperature_range' => 'encrypted',
            'precipitation' => 'encrypted',
            'daylight' => 'encrypted',
        ];
    }

    /**
     * Get the journal entry that owns the weather module.
     *
     * @return BelongsTo<JournalEntry, $this>
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
