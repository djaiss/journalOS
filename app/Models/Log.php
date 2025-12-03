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
 * @property int|null $organization_id
 * @property int|null $user_id
 * @property string $user_name
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
        'organization_id',
        'user_id',
        'user_name',
        'action',
        'description',
    ];

    /**
     * Get the organization associated with the log.
     *
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

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
     * Get the user name associated with the log.
     * If the user object exists, return the name from the user object.
     * If the user object does not exist, return the user name that was set in
     * the log at the time of creation.
     *
     * @return string
     */
    public function getUserName(): string
    {
        return $this->user ? $this->user->getFullName() : $this->user_name;
    }
}
