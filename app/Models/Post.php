<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'journal_id',
        'title',
        'number_of_words',
        'reading_time_in_seconds',
        'is_published',
        'written_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_published' => 'boolean',
        'written_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the post's journal.
     */
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    /**
     * Get the post's sections.
     */
    public function postSections(): HasMany
    {
        return $this->hasMany(PostSection::class);
    }

    /**
     * Get the post's title.
     *
     * @return Attribute<string,string>
     */
    protected function title(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value) {
                    return $value;
                }

                return trans('Undefined');
            },
            set: fn ($value) => $value,
        );
    }

    /**
     * Get the post's body excerpt.
     *
     * @return Attribute<string,never>
     */
    protected function excerpt(): Attribute
    {
        return Attribute::make(
            get: fn () => Str::limit(optional($this->postSections()->whereNotNull('content')->first())->content, 200)
        );
    }
}
