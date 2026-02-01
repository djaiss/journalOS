<?php

declare(strict_types = 1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ModuleSocialDensity
 *
 * Represents social density data for a journal entry.
 *
 * @property int $id
 * @property int $journal_entry_id
 * @property string $category # Format: string
 * @property string|null $social_density # Format: 'alone', 'few people', 'crowd', 'too much'
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class ModuleSocialDensity extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleSocialDensityFactory> */
    use HasFactory;

    public const array SOCIAL_DENSITY_VALUES = [
        'alone',
        'few people',
        'crowd',
        'too much',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'module_social_density';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_entry_id',
        'category',
        'social_density',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'social_density' => 'encrypted',
        ];
    }

    /**
     * Get the journal entry that owns the social density module.
     *
     * @return BelongsTo<JournalEntry, $this>
     */
    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
