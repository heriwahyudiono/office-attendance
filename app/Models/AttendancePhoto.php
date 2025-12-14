<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendancePhoto extends Model
{
    protected $fillable = ['attendance_id', 'photo'];
    const UPDATED_AT = null;

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
