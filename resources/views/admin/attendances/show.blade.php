<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Attendance Detail') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-lg font-bold mb-4 border-b pb-2">Photo Evidence</h3>
                            @if($attendance->photo)
                            <img src="{{ route('admin.attendances.photo', $attendance->id) }}" alt="Attendance Photo" class="w-full rounded-lg shadow-lg border border-gray-200">
                            @else
                            <div class="bg-gray-100 p-6 text-center text-gray-500 rounded-lg">
                                No photo available
                            </div>
                            @endif
                        </div>

                        <div>
                            <h3 class="text-lg font-bold mb-4 border-b pb-2">Details</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Employee</dt>
                                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $attendance->user->name }}</dd>
                                    <dd class="text-sm text-gray-500">{{ $attendance->user->email }}</dd>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Date</dt>
                                        <dd class="mt-1 text-gray-900">{{ $attendance->attendance_date }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Time</dt>
                                        <dd class="mt-1 text-gray-900">{{ $attendance->attendance_time }}</dd>
                                    </div>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1">
                                        @if($attendance->status == 'IN')
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">CHECK IN</span>
                                        @else
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">CHECK OUT</span>
                                        @endif
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Location</dt>
                                    <dd class="mt-1 text-gray-900">{{ $attendance->officeLocation->name ?? 'Unknown Location' }}</dd>
                                    <dd class="text-xs text-gray-500 mt-1">
                                        Lat: {{ $attendance->latitude }}, Long: {{ $attendance->longitude }}
                                        <br>
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ $attendance->latitude }},{{ $attendance->longitude }}" target="_blank" class="text-blue-600 hover:underline">
                                            Open in Google Maps
                                        </a>
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Device Info</dt>
                                    <dd class="mt-1 text-xs text-mono bg-gray-50 p-2 rounded border border-gray-200 break-words">
                                        {{ $attendance->device_info }}
                                    </dd>
                                </div>
                            </dl>

                            <div class="mt-8 pt-4 border-t">
                                <form action="{{ route('admin.attendances.destroy', $attendance->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?')">
                                    @csrf
                                    @method('DELETE')
                                    <a href="{{ route('admin.attendances.index') }}" class="mr-4 text-gray-600 hover:text-gray-900">Back to List</a>
                                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Delete Record</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>