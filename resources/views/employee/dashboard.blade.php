<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Employee Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Quick Attendance Action -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 flex flex-col items-center">
                            <h3 class="text-lg font-bold mb-4">Mark Attendance</h3>
                            <a href="{{ route('attendance.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-full hover:bg-blue-700 transition">
                                📷 Scan Face & Location
                            </a>
                        </div>

                        <!-- History Action -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 flex flex-col items-center">
                            <h3 class="text-lg font-bold mb-4">Attendance History</h3>
                            <a href="{{ route('attendance.history') }}" class="text-blue-600 hover:underline">
                                View Logs &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>