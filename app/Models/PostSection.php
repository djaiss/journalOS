<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostSection extends Model
{
    use HasFactory;

    protected $table = 'post_sections';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'post_id',
        'label',
        'position',
        'content',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}