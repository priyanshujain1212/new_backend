<?php

namespace App\Models;

use App\Models\BaseModel;

class Template extends BaseModel
{
    protected $table       = 'templates';
    protected $auditColumn = false;
    protected $fillable    = ['name'];
}
