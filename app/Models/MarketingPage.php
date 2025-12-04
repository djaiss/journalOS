<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Class MarketingPage
 *
 * @property int $id
 * @property string $url
 * @property int $pageviews
 * @property bool $marked_helpful
 * @property bool $marked_not_helpful
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class MarketingPage extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'marketing_pages';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'url',
        'pageviews',
        'marked_helpful',
        'marked_not_helpful',
    ];
}
