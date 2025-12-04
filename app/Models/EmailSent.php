<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class EmailSent
 *
 * Represents an email that has been sent in the system.
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $uuid
 * @property string $email_type
 * @property string $email_address
 * @property string $subject
 * @property string $body
 * @property \Illuminate\Support\Carbon|null $sent_at
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property \Illuminate\Support\Carbon|null $bounced_at
 */
final class EmailSent extends Model
{
    /** @use HasFactory<\Database\Factories\EmailSentFactory> */
    use HasFactory;

    protected $table = 'emails_sent';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'uuid',
        'email_type',
        'email_address',
        'subject',
        'body',
        'sent_at',
        'delivered_at',
        'bounced_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'bounced_at' => 'datetime',
    ];

    /**
     * Get the user associated with the email.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
