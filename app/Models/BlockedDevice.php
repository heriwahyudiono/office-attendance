<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedDevice extends Model
{
    protected $fillable = ['device_info', 'reason'];
    const UPDATED_AT = null;
}
