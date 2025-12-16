<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Employee Dashboard') }}
        </h2>
    </x-slot>
    @php
        $checkIn  = $todayAttendance['IN'] ?? null;
        $checkOut = $todayAttendance['OUT'] ?? null;

        if (!$checkIn) {
            $greeting = 'Selamat pagi';
            $message  = 'Silakan lakukan absensi masuk';
        } elseif ($checkIn && !$checkOut) {
            $greeting = now()->hour < 18 ? 'Selamat sore' : 'Selamat malam';
            $message  = 'Jangan lupa melakukan absensi pulang';
        } else {
            $greeting = 'Terima kasih';
            $message  = 'Absensi hari ini sudah lengkap';
        }
    @endphp


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Welcome Info Card -->
                    <div class="mb-6 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-xl p-6 shadow">
                        <p class="text-sm opacity-90">{{$greeting}}</p>

                        <h2 class="text-2xl font-bold mt-1">
                            {{ auth()->user()->name }}
                        </h2>

                        <p class="mt-2 text-sm opacity-90">
                            {{ $message }}
                        </p>
                    </div>
                    <!-- attendance time card -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6"> 
                        <!-- Check In Time -->
                        <div class="bg-white border rounded-xl p-6 shadow-sm">
                            <p class="text-sm text-gray-400 flex items-center gap-1">Clock In</p>

                            @if($checkIn)
                                <p class="text-2xl font-bold text-green-600">
                                    {{ \Carbon\Carbon::parse($checkIn->attendance_time)->format('H:i') }}
                                </p>
                            @else
                                <p class="text-lg text-gray-400 italic">
                                    Not yet
                                </p>
                            @endif
                        </div>
                        <!-- Check Out Time -->
                        <div class="bg-white border rounded-xl p-6 shadow-sm">
                            <p class="text-sm text-gray-400">Clock Out</p>
                            @if($checkOut)
                                <p class="text-2xl font-bold text-blue-600">
                                    {{ \Carbon\Carbon::parse($checkOut->attendance_time)->format('H:i') }}
                                        </p>
                                    @else
                                        <p class="text-lg text-gray-400 italic">
                                            Not yet
                                        </p>
                                    @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Quick Attendance Action -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 flex flex-col items-center">
                            <h3 class="text-lg font-bold mb-4">Mark Attendance</h3>
                            <!-- logic tombol attendance -->
                            @if($checkIn && $checkOut)
                                 <!-- jika sudah in & out -->
                                <button 
                                disabled 
                                class="bg-gray-400 text-white px-6 py-3 rounded-full cursor-not-allowed">
                                ✅ Attendance Completed
                                </button>
                            @else
                                <a 
                                    href="{{ route('attendance.create') }}" 
                                    class="bg-blue-600 text-white px-6 py-3 rounded-full hover:bg-blue-700 transition">
                                    📷 Scan Face & Location
                                </a>
                         @endif
                    </div>

                    <!-- History Action -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 flex flex-col items-center">
                        <h3 class="text-lg font-bold mb-4">
                            Attendance History
                        </h3>
                         <a 
                            href="{{ route('attendance.history') }}" 
                            class="text-blue-600 hover:underline">
                            View Logs &rarr;
                        </a>
                    </div>
                                
                </div>
            </div>
                           
        </div>                
</x-app-layout>