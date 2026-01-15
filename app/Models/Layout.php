<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Layout
 *
 * Represents a journal layout configuration.
 *
 * @property int $id
 * @property int $journal_id
 * @property string $name
 * @property int $columns_count
 * @property bool $is_active
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class Layout extends Model
{
    /** @use HasFactory<\Database\Factories\LayoutFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'layouts';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_id',
        'name',
        'columns_count',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'name' => 'encrypted',
            'columns_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the journal associated with the layout.
     *
     * @return BelongsTo<Journal, $this>
     */
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }
}
