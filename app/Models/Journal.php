<?php

declare(strict_types=1);

namespace App\Models;

use App\Actions\GenerateJournalAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * Class Journal
 *
 * Represents a journal entry in the system.
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $name
 * @property ?string $slug
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
    ];

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
     * Get the journal avatar.
     */
    public function avatar(): string
    {
        return new GenerateJournalAvatar($this->id . '-' . $this->name)->execute();
    }
}
