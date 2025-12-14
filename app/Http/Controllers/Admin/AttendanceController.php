<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with(['user', 'officeLocation'])->latest()->paginate(20);
        return view('admin.attendances.index', compact('attendances'));
    }

    public function show($id)
    {
        $attendance = Attendance::with(['user', 'officeLocation', 'photo'])->findOrFail($id);
        return view('admin.attendances.show', compact('attendance'));
    }

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);

        // Delete photo from storage if exists
        if ($attendance->photo) {
            Storage::delete('private/' . $attendance->photo->photo);
        }

        $attendance->delete();

        return redirect()->route('admin.attendances.index')->with('success', 'Attendance deleted successfully.');
    }

    public function photo($id)
    {
        $attendance = Attendance::with('photo')->findOrFail($id);

        if (!$attendance->photo) {
            abort(404, 'Photo not found');
        }

        // Path in DB: attendance_photos/YYYY...
        // Full Path: private/attendance_photos/YYYY...
        $path = 'private/' . $attendance->photo->photo;

        if (!Storage::exists($path)) {
            abort(404, 'File not found on storage');
        }

        return Storage::download($path);
    }
}
