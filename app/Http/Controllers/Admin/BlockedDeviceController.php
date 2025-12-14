<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedDevice;
use Illuminate\Http\Request;

class BlockedDeviceController extends Controller
{
    public function index()
    {
        $devices = BlockedDevice::latest()->paginate(20);
        return view('admin.blocked_devices.index', compact('devices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'device_info' => 'required|string|unique:blocked_devices,device_info',
            'reason' => 'nullable|string',
        ]);

        BlockedDevice::create($request->all());

        return redirect()->route('admin.blocked-devices.index')->with('success', 'Device blocked successfully.');
    }

    public function destroy($id)
    {
        BlockedDevice::findOrFail($id)->delete();
        return redirect()->route('admin.blocked-devices.index')->with('success', 'Device unblocked successfully.');
    }
}
