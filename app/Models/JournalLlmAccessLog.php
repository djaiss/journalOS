<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class JournalLlmAccessLog
 *
 * @property int $id
 * @property int $journal_id
 * @property int $requested_year
 * @property int|null $requested_month
 * @property int|null $requested_day
 * @property string $request_url
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class JournalLlmAccessLog extends Model
{
    /** @use HasFactory<\Database\Factories\JournalLlmAccessLogFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'journal_llm_access_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'journal_id',
        'requested_year',
        'requested_month',
        'requested_day',
        'request_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'request_url' => 'encrypted',
        ];
    }

    /**
     * Get the journal associated with the access log.
     *
     * @return BelongsTo<Journal, $this>
     */
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }
}
