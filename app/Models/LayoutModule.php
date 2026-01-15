<?php

declare(strict_types=1);

namespace App\Models;

use App\Helpers\ModuleCatalog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class LayoutModule
 *
 * Represents a module position within a layout.
 *
 * @property int $id
 * @property int $layout_id
 * @property string $module_key
 * @property int $column_number
 * @property int $position
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 */
final class LayoutModule extends Model
{
    /** @use HasFactory<\Database\Factories\LayoutModuleFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'layout_modules';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'layout_id',
        'module_key',
        'column_number',
        'position',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'column_number' => 'integer',
            'position' => 'integer',
        ];
    }

    /**
     * Get the layout associated with the module.
     *
     * @return BelongsTo<Layout, $this>
     */
    public function layout(): BelongsTo
    {
        return $this->belongsTo(Layout::class);
    }

    /**
     * @return array<int, string>
     */
    public static function allowedModuleKeys(): array
    {
        return ModuleCatalog::moduleKeys();
    }
}
