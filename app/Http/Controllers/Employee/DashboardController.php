<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = now()->toDateString();

        $checkIn = Attendance::where('user_id', $user->id)
            ->where('attendance_date', $today)
            ->where('status', 'IN')
            ->first();

        $checkOut = Attendance::where('user_id', $user->id)
            ->where('attendance_date', $today)
            ->where('status', 'OUT')
            ->first();

    return view('employee.dashboard', compact('checkIn', 'checkOut'));

    }
}
