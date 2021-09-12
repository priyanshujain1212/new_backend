<?php

namespace App\Models;

use App\Models\BaseModel;
use App\User;

class RequestWithdraw extends BaseModel
{
    protected $table       = 'request_withdraw';
    protected $auditColumn = true;
    protected $fillable    = ['user_id', 'amount', 'status', 'date'];
    protected $dates       = [
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute()
    {
        return trans('request_withdraw_statuses.' . $this->status);
    }
}
