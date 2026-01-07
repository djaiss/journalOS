<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Date;

/**
 * Class JournalEntry
 *
 * Most properties are encrypted to protect the privacy of the user.
 * This forces us to use strings to represent the values, even when it's not
 * the right data type.
 *
 * @property int $id
 * @property int $journal_id
 * @property int $day
 * @property int $month
 * @property int $year
 * @property bool $has_content
 * @property string|null $had_kids_today # Format: 'yes'|'no'
 * @property string|null $primary_obligation # Format: 'work', 'family', 'personal', 'health', 'travel', 'none'
 * @property string|null $health # Format: 'good', 'okay', 'not great'
 * @property string|null $had_sexual_activity # Format: 'yes'|'no'
 * @property string|null $sexual_activity_type # Format: 'solo', 'with-partner', 'intimate-contact'
 * @property string|null $social_density # Format: 'alone', 'few people', 'crowd', 'too much'
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class JournalEntry extends Model
{
    /** @use HasFactory<\Database\Factories\JournalEntryFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'journal_entries';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_id',
        'day',
        'month',
        'year',
        'has_content',
        'had_kids_today',
        'primary_obligation',
        'health',
        'had_sexual_activity',
        'sexual_activity_type',
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
            'has_content' => 'boolean',
            'had_kids_today' => 'encrypted',
            'primary_obligation' => 'encrypted',
            'had_sexual_activity' => 'encrypted',
            'sexual_activity_type' => 'encrypted',
            'social_density' => 'encrypted',
        ];
    }

    /**
     * Get the journal associated with the entry.
     *
     * @return BelongsTo<Journal, $this>
     */
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    /**
     * Get the books associated with the entry.
     *
     * @return BelongsToMany<Book, $this>
     */
    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_journal_entry')
            ->withPivot('status')
            ->withTimestamps();
    }

    /**
     * Get the sleep module data for this entry.
     *
     * @return HasOne<ModuleSleep, $this>
     */
    public function moduleSleep(): HasOne
    {
        return $this->hasOne(ModuleSleep::class, 'journal_entry_id');
    }

    /**
     * Get the energy module data for this entry.
     *
     * @return HasOne<ModuleEnergy, $this>
     */
    public function moduleEnergy(): HasOne
    {
        return $this->hasOne(ModuleEnergy::class, 'journal_entry_id');
    }

    /**
     * Get the mood module data for this entry.
     *
     * @return HasOne<ModuleMood, $this>
     */
    public function moduleMood(): HasOne
    {
        return $this->hasOne(ModuleMood::class, 'journal_entry_id');
    }

    /**
     * Get the health module data for this entry.
     *
     * @return HasOne<ModuleHealth, $this>
     */
    public function moduleHealth(): HasOne
    {
        return $this->hasOne(ModuleHealth::class, 'journal_entry_id');
    }

    /**
     * Get the day type module data for this entry.
     *
     * @return HasOne<ModuleDayType, $this>
     */
    public function moduleDayType(): HasOne
    {
        return $this->hasOne(ModuleDayType::class, 'journal_entry_id');
    }

    /**
     * Get the travel module data for this entry.
     *
     * @return HasOne<ModuleTravel, $this>
     */
    public function moduleTravel(): HasOne
    {
        return $this->hasOne(ModuleTravel::class, 'journal_entry_id');
    }

    /**
     * Get the physical activity module data for this entry.
     *
     * @return HasOne<ModulePhysicalActivity, $this>
     */
    public function modulePhysicalActivity(): HasOne
    {
        return $this->hasOne(ModulePhysicalActivity::class, 'journal_entry_id');
    }

    /**
     * Get the work module data for this entry.
     *
     * @return HasOne<ModuleWork, $this>
     */
    public function moduleWork(): HasOne
    {
        return $this->hasOne(ModuleWork::class, 'journal_entry_id');
    }

    /**
     * Get the date of the entry in a human readable format, like "2024/12/23".
     */
    public function getDate(): string
    {
        return Date::create($this->year, $this->month, $this->day)
            ->format('l F jS, Y');
    }
}
