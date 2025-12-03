<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
 * @property string $first_name
 * @property string $last_name
 * @property string $nickname
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string|null $two_factor_secret
 * @property array|null $two_factor_recovery_codes
 * @property string|null $two_factor_preferred_method
 * @property Carbon|null $two_factor_confirmed_at
 * @property Carbon $created_at
 * @property string $password
 * @property string $locale
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_recovery_codes' => 'array',
        ];
    }

    /**
     * Get the organizations associated with the user.
     *
     * @return BelongsToMany<Organization, $this>
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class)
            ->withPivot(['joined_at'])
            ->withTimestamps();
    }

    /**
     * Get the emailsSent associated with the user.
     */
    public function emailsSent(): HasMany
    {
        return $this->hasMany(EmailSent::class);
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
     * Check if the user is part of a specific organization.
     *
     * @return bool
     */
    public function isPartOfOrganization(Organization $organization): bool
    {
        return $this->organizations()->where('organization_id', $organization->id)->exists();
    }
}
