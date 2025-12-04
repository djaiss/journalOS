<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * Class Log
 *
 * Represents a log entry in the system for tracking user actions and events.
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $journal_id
 * @property string $journal_name
 * @property string $action
 * @property string $description
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class Log extends Model
{
    /** @use HasFactory<\Database\Factories\LogFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'journal_id',
        'action',
        'description',
        'journal_name',
    ];

    /**
     * Get the user associated with the log.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the journal associated with the log.
     *
     * @return BelongsTo<Journal, $this>
     */
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    /**
     * Get the journal name associated with the log.
     * If the journal object exists, return the name from the journal object.
     * If the journal object does not exist, return the journal name that was
     * set in the log at the time of creation.
     *
     * @return string
     */
    public function getJournalName(): string
    {
        return $this->journal ? $this->journal->name : $this->journal_name;
    }
}
