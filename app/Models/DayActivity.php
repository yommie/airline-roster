<?php

namespace App\Models;

use App\Enums\ActivityTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DayActivity extends Model
{
    public const ACTIVITY_TYPE_MAP = [
        ActivityTypeEnum::Flight->value => Flight::class
    ];

    protected $fillable = [
        'user_id',
        'date',
        'activity_type',
        'activity_type_id',
        'activity_start',
        'activity_end',
        'extra_data',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
