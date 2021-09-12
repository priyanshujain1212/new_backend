<?php

namespace App\Models;

use App\User;
use Shipu\Watchable\Traits\WatchableTrait;

class Collection extends BaseModel
{
    use WatchableTrait;

    protected $auditColumn = true;
    protected $table       = 'collection';

    protected $dates = [
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCollectionOwner($query)
    {
        $roleID = auth()->user()->myrole ?? 0;
        if ($roleID == 4) {
            $query->where('user_id', auth()->id());
        }
    }
}
