<?php

declare(strict_types=1);

namespace App\Models;

use App\Actions\GenerateJournalAvatar;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Journal
 *
 * Represents a journal entry in the system.
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $name
 * @property ?string $slug
 * @property bool $show_sleep_module
 * @property bool $show_work_module
 * @property bool $show_travel_module
 * @property bool $show_day_type_module
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class Journal extends Model
{
    /** @use HasFactory<\Database\Factories\JournalFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'journals';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'show_sleep_module',
        'show_work_module',
        'show_travel_module',
        'show_day_type_module',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'name' => 'encrypted',
            'slug' => 'encrypted',
            'show_sleep_module' => 'boolean',
            'show_work_module' => 'boolean',
            'show_travel_module' => 'boolean',
            'show_day_type_module' => 'boolean',
        ];
    }

    /**
     * Get the user associated with the journal.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the journal entries associated with the journal.
     *
     * @return HasMany<JournalEntry, $this>
     */
    public function entries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    /**
     * Get the journal avatar.
     */
    public function avatar(): string
    {
        return new GenerateJournalAvatar($this->id . '-' . $this->name)->execute();
    }
}
