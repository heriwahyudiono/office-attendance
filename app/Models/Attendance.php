<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'office_location_id',
        'attendance_date',
        'attendance_time',
        'latitude',
        'longitude',
        'distance_meter',
        'status',
        'device_info'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function officeLocation()
    {
        return $this->belongsTo(OfficeLocation::class);
    }

    public function photo()
    {
        return $this->hasOne(AttendancePhoto::class);
    }
}
