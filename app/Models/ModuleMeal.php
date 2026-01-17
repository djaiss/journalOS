<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ModuleMeal
 *
 * Represents meal tracking data for a journal entry.
 *
 * @property int $id
 * @property int $journal_entry_id
 * @property string $category # Format: string
 * @property string|null $breakfast # Format: 'yes'|'no'
 * @property string|null $lunch # Format: 'yes'|'no'
 * @property string|null $dinner # Format: 'yes'|'no'
 * @property string|null $snack # Format: 'yes'|'no'
 * @property string|null $meal_type # Format: 'home_cooked'|'takeout'|'restaurant'|'work_cafeteria'
 * @property string|null $social_context # Format: 'alone'|'family'|'friends'|'colleagues'
 * @property string|null $notes # Format: string
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class ModuleMeal extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleMealFactory> */
    use HasFactory;

    public const array MEAL_PRESENCE = [
        'yes',
        'no',
    ];

    public const array MEAL_TYPES = [
        'home_cooked',
        'takeout',
        'restaurant',
        'work_cafeteria',
    ];

    public const array SOCIAL_CONTEXTS = [
        'alone',
        'family',
        'friends',
        'colleagues',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_meal';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_entry_id',
        'category',
        'breakfast',
        'lunch',
        'dinner',
        'snack',
        'meal_type',
        'social_context',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'breakfast' => 'encrypted',
            'lunch' => 'encrypted',
            'dinner' => 'encrypted',
            'snack' => 'encrypted',
            'meal_type' => 'encrypted',
            'social_context' => 'encrypted',
            'notes' => 'encrypted',
        ];
    }

    /**
     * Get the journal entry that owns the meal module.
     *
     * @return BelongsTo<JournalEntry, $this>
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
