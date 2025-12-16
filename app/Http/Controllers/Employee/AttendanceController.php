<?php

namespace App\Http\Controllers\Employee;

use App\Helpers\DistanceHelper;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendancePhoto;
use App\Models\BlockedDevice;
use App\Models\OfficeLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = now()->toDateString();

        $todayAttendance = Attendance::where('user_id', $user->id)
         ->where('attendance_date', $today)
         ->get()
         ->keyBy('status'); // in out
        
         return view('employee.dashboard', compact('todayAttendance'));
    }
    public function create()
    {
        $officeLocations = OfficeLocation::select('id', 'name', 'latitude', 'longitude', 'radius_meter')->get();
        $allowAnyLocation = env('ALLOW_ANY_LOCATION', false);
        return view('employee.attendance', compact('officeLocations', 'allowAnyLocation'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'photo' => 'required|string', // base64
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'device_info' => 'required|string',
            'status' => 'required|in:IN,OUT',
        ]);

        $user = auth()->user();
        $today = now()->format('Y-m-d');

        // 1. Check Blocked Device
        $blocked = BlockedDevice::where('device_info', $request->device_info)->exists();
        if ($blocked) {
            return back()->withErrors(['device_info' => 'This device is blocked inside the system.']);
        }

        // 2. Prevent Duplicate
        $exists = Attendance::where('user_id', $user->id)
            ->where('attendance_date', $today)
            ->where('status', $request->status)
            ->exists();

        if ($exists) {
            return back()->withErrors(['status' => 'You have already checked ' . $request->status . ' today.']);
        }

        // 3. Geofencing Validation
        $officeLocations = OfficeLocation::all();
        $validLocation = null;
        $minDistance = 0;
        $allowAnyLocation = config('app.allow_any_location');

        // Safety: office harus ada
        if ($officeLocations->isEmpty()) {
            return back()->withErrors([
                'location' => 'No office location configured in the system.'
            ]);
        }

        if($allowAnyLocation){
            // first office untuk default
            $validLocation = $officeLocations->first();
            $minDistance = null; 
        }else{
             foreach ($officeLocations as $location) {
            $distance = DistanceHelper::calculateDistance(
                $request->latitude,
                $request->longitude,
                $location->latitude,
                $location->longitude
            );
            
            if ($distance <= $location->radius_meter) {
                if ($minDistance === null || $distance < $minDistance) {
                    $validLocation = $location;
                    $minDistance = $distance;
                }
            }
        }
    }

    // Final guard
    if (!$validLocation) {
        return back()->withErrors([
            'location' => 'You are outside the office geofence radius.'
        ]);
    }
        // 4. Decode Photo & Prepare Path
        $photoData = $request->photo; // "data:image/png;base64,....."
        if (preg_match('/^data:image\/(\w+);base64,/', $photoData, $type)) {
            $photoData = substr($photoData, strpos($photoData, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, etc.

            if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                return back()->withErrors(['photo' => 'Invalid image format.']);
            }

            $base64Image = base64_decode($photoData);
            if ($base64Image === false) {
                return back()->withErrors(['photo' => 'Invalid base64 image.']);
            }
        } else {
            return back()->withErrors(['photo' => 'Invalid photo data.']);
        }

        // Path: private/attendance_photos/YYYYMMDD_HHMMSSmmm.png
        $filename =  now()->format('Ymd_Hisv') . '.' . $type;
        $relativePath = 'attendance_photos/' . $filename;
        $storagePath = 'private/' . $relativePath;

        // 5. Transaction
        try {
            DB::transaction(function () use ($user, $validLocation, $request, $minDistance, $relativePath, $storagePath, $base64Image, $today) {
                // Save Attendance
                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'office_location_id' => $validLocation->id,
                    'attendance_date' => $today,
                    'attendance_time' => now()->format('H:i:s'),
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'distance_meter' => $minDistance ?? 0,
                    'status' => $request->status,
                    'device_info' => $request->device_info,
                ]);

                // Save Photo Record
                AttendancePhoto::create([
                    'attendance_id' => $attendance->id,
                    'photo' => $relativePath, // Save relative path to DB
                ]);

                // Store File
                Storage::put($storagePath, $base64Image);
            });

            return redirect()->route('dashboard')->with('success', 'Attendance ' . $request->status . ' submitted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['system' => 'System error: ' . $e->getMessage()]);
        }
    }

    public function history()
    {
        $attendances = auth()->user()->attendances()->with('officeLocation')->latest()->paginate(10);
        return view('employee.attendance_history', compact('attendances'));
    }
}
