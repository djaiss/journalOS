<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class User
 *
 * @property int $id
 * @property bool $is_instance_admin
 * @property string $first_name
 * @property string $last_name
 * @property string $nickname
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string|null $two_factor_secret
 * @property array|null $two_factor_recovery_codes
 * @property string|null $two_factor_preferred_method
 * @property Carbon|null $two_factor_confirmed_at
 * @property Carbon|null $last_activity_at
 * @property string $password
 * @property string $locale
 * @property bool $auto_delete_account
 * @property bool $has_lifetime_access
 * @property string|null $last_used_ip
 * @property Carbon|null $trial_ends_at
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'is_instance_admin',
        'first_name',
        'last_name',
        'nickname',
        'email',
        'password',
        'locale',
        'email_verified_at',
        'two_factor_preferred_method',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'last_activity_at',
        'auto_delete_account',
        'has_lifetime_access',
        'last_used_ip',
        'trial_ends_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_instance_admin' => 'boolean',
            'first_name' => 'encrypted',
            'last_name' => 'encrypted',
            'nickname' => 'encrypted',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_recovery_codes' => 'array',
            'last_activity_at' => 'datetime',
            'auto_delete_account' => 'boolean',
            'last_used_ip' => 'encrypted',
            'has_lifetime_access' => 'boolean',
            'trial_ends_at' => 'datetime',
        ];
    }

    /**
     * Get the journals associated with the user.
     *
     * @return HasMany<Journal, $this>
     */
    public function journals(): HasMany
    {
        return $this->hasMany(Journal::class);
    }

    /**
     * Get the emailsSent associated with the user.
     * @return HasMany<EmailSent, $this>
     */
    public function emailsSent(): HasMany
    {
        return $this->hasMany(EmailSent::class);
    }

    /**
    * Get the logs associated with the user.
    *
    * @return HasMany<Log, $this>
    */
    public function logs(): HasMany
    {
        return $this->hasMany(Log::class);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->first_name . ' ' . $this->last_name)
            ->explode(' ')
            ->map(fn(string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    /**
     * Get the user's full name by combining first and last name.
     * If a nickname is set, it will be used instead of the full name.
     */
    public function getFullName(): string
    {
        if ($this->nickname) {
            return $this->nickname;
        }

        $firstName = $this->first_name;
        $lastName = $this->last_name;
        $separator = $firstName && $lastName ? ' ' : '';

        return $firstName . $separator . $lastName;
    }

    /**
     * Check if the user is in trial.
     *
     * @return bool
     */
    public function isInTrial(): bool
    {
        return config('journalos.enable_paid_version')
            && ! $this->has_lifetime_access
            && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if the user needs to pay to continue using the app.
     *
     * @return bool
     */
    public function needsToPay(): bool
    {
        return config('journalos.enable_paid_version')
            && ! $this->has_lifetime_access
            && $this->trial_ends_at->isPast();
    }
}
