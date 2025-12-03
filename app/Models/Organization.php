<?php

declare(strict_types=1);

namespace App\Models;

use App\Actions\GenerateOrganizationAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Organization
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class Organization extends Model
{
    /** @use HasFactory<\Database\Factories\OrganizationFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'organizations';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [];
    }

    /**
     * Get the users associated with the organization.
     *
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['joined_at'])
            ->withTimestamps();
    }

    /**
     * Get the emails sent by the organization.
     *
     * @return HasMany<EmailSent, $this>
     */
    public function emailsSent(): HasMany
    {
        return $this->hasMany(EmailSent::class, 'organization_id');
    }

    public function getAvatar(): string
    {
        return new GenerateOrganizationAvatar($this->id . '-' . $this->name)->execute();
    }
}
