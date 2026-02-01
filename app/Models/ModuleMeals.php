<?php

declare(strict_types = 1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ModuleMeals
 *
 * Represents meal tracking data for a journal entry.
 *
 * @property int $id
 * @property int $journal_entry_id
 * @property string $category # Format: string
 * @property array<string>|null $meal_presence # Format: ['breakfast','lunch','dinner','snack']
 * @property string|null $meal_type # Format: 'home_cooked'|'takeout'|'restaurant'|'work_cafeteria'
 * @property string|null $social_context # Format: 'alone'|'family'|'friends'|'colleagues'
 * @property string|null $has_notes # Format: 'yes'|'no'
 * @property string|null $notes # Format: 'Had a late dinner...'
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class ModuleMeals extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleMealsFactory> */
    use HasFactory;

    public const array MEAL_PRESENCE = [
        'breakfast',
        'lunch',
        'dinner',
        'snack',
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
    protected $table = 'module_meals';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_entry_id',
        'category',
        'meal_presence',
        'meal_type',
        'social_context',
        'has_notes',
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
            'meal_presence' => 'encrypted:array',
            'meal_type' => 'encrypted',
            'social_context' => 'encrypted',
            'has_notes' => 'encrypted',
            'notes' => 'encrypted',
        ];
    }

    /**
     * Get the journal entry that owns the meals module.
     *
     * @return BelongsTo<JournalEntry, $this>
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
