<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ModuleShopping
 *
 * Represents shopping tracking data for a journal entry.
 *
 * @property int $id
 * @property int $journal_entry_id
 * @property string $category # Format: string
 * @property string|null $has_shopped_today # Format: 'yes'|'no'
 * @property array<string>|null $shopping_type # Format: ['groceries', 'clothes']
 * @property string|null $shopping_intent # Format: 'planned'|'opportunistic'|'impulse'|'replacement'
 * @property string|null $shopping_context # Format: 'alone'|'with_partner'|'with_kids'
 * @property string|null $shopping_for # Format: 'for_self'|'for_household'|'for_others'
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class ModuleShopping extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleShoppingFactory> */
    use HasFactory;

    public const array SHOPPING_TYPES = [
        'groceries',
        'clothes',
        'electronics_tech',
        'household_essentials',
        'books_media',
        'gifts',
        'online_shopping',
        'other',
    ];

    public const array SHOPPING_INTENTS = [
        'planned',
        'opportunistic',
        'impulse',
        'replacement',
    ];

    public const array SHOPPING_CONTEXTS = [
        'alone',
        'with_partner',
        'with_kids',
    ];

    public const array SHOPPING_FOR_OPTIONS = [
        'for_self',
        'for_household',
        'for_others',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_shopping';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_entry_id',
        'category',
        'has_shopped_today',
        'shopping_type',
        'shopping_intent',
        'shopping_context',
        'shopping_for',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'has_shopped_today' => 'encrypted',
            'shopping_type' => 'encrypted:array',
            'shopping_intent' => 'encrypted',
            'shopping_context' => 'encrypted',
            'shopping_for' => 'encrypted',
        ];
    }

    /**
     * Get the journal entry that owns the shopping module.
     *
     * @return BelongsTo<JournalEntry, $this>
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
