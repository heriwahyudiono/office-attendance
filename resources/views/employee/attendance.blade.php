<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mark Attendance') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-lg font-bold mb-4">1. Camera</h3>
                            <div class="relative w-full h-64 bg-black rounded-lg overflow-hidden">
                                <video id="video" class="w-full h-full object-cover" autoplay playsinline></video>
                                <canvas id="canvas" class="hidden"></canvas>
                            </div>
                            <button id="snap" class="mt-4 bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 w-full">
                                Capture Photo
                            </button>
                            <img id="photo-preview" class="mt-4 w-full h-64 object-cover rounded-lg hidden border-2 border-green-500" alt="Captured Photo">
                        </div>

                        <div>
                            <h3 class="text-lg font-bold mb-4">2. Location & Submit</h3>

                            <div id="location-status" class="mb-4 p-3 bg-yellow-100 text-yellow-800 rounded">
                                Waiting for GPS...
                            </div>

                            <form action="{{ route('attendance.store') }}" method="POST" id="attendance-form" class="hidden">
                                @csrf
                                <input type="hidden" name="photo" id="photo-input">
                                <input type="hidden" name="latitude" id="lat-input">
                                <input type="hidden" name="longitude" id="long-input">
                                <input type="hidden" name="device_info" id="device-input">

                                <div class="mb-6">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Attendance Status</label>
                                    <div class="flex gap-4">
                                        <label class="flex items-center">
                                            <input type="radio" name="status" value="IN" class="mr-2" checked>
                                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">CHECK IN</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="status" value="OUT" class="mr-2">
                                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-semibold">CHECK OUT</span>
                                        </label>
                                    </div>
                                </div>

                                <button type="submit" id="submit-btn" class="w-full bg-green-600 text-white font-bold py-3 px-4 rounded hover:bg-green-700 disabled:opacity-50" disabled>
                                    Submit Attendance
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const snap = document.getElementById('snap');
        const photoPreview = document.getElementById('photo-preview');
        const photoInput = document.getElementById('photo-input');
        const latInput = document.getElementById('lat-input');
        const longInput = document.getElementById('long-input');
        const deviceInput = document.getElementById('device-input');
        const locationStatus = document.getElementById('location-status');
        const attendanceForm = document.getElementById('attendance-form');
        const submitBtn = document.getElementById('submit-btn');

        // 1. Setup Camera
        navigator.mediaDevices.getUserMedia({
                video: true
            })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(err => {
                console.error("Camera Error: ", err);
                alert("Please allow camera access.");
            });

        // 2. Capture Photo
        snap.addEventListener('click', () => {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, video.videoWidth, video.videoHeight);

            const dataURL = canvas.toDataURL('image/png');
            photoPreview.src = dataURL;
            photoPreview.classList.remove('hidden');
            video.classList.add('hidden');
            photoInput.value = dataURL;

            checkReady();
        });

        // 3. Setup GPS
        // 3. Setup GPS
        const officeLocations = @json($officeLocations);
        const allowAnyLocation = @json($allowAnyLocation);

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371000; // Radius of the earth in meters
            const dLat = (lat2 - lat1) * (Math.PI / 180);
            const dLon = (lon2 - lon1) * (Math.PI / 180);
            const a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c; // Distance in meters
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(position => {
                const lat = position.coords.latitude;
                const long = position.coords.longitude;

                latInput.value = lat;
                longInput.value = long;

                // Find nearest office
                let minDistance = Infinity;
                let nearestOffice = null;
                let inRadius = false;

                officeLocations.forEach(office => {
                    const distance = calculateDistance(lat, long, parseFloat(office.latitude), parseFloat(office.longitude));
                    if (distance < minDistance) {
                        minDistance = distance;
                        nearestOffice = office;
                    }
                });

                if (nearestOffice) {
                    inRadius = minDistance <= nearestOffice.radius_meter;
                }

                if (inRadius || allowAnyLocation) {
                    let message = `
                        <strong>✅ You are in range!</strong><br>
                        Nearest Office: ${nearestOffice ? nearestOffice.name : 'Unknown'}<br>
                        Distance: ${Math.round(minDistance)} meters (Max: ${nearestOffice ? nearestOffice.radius_meter : 0}m)
                    `;

                    if (!inRadius && allowAnyLocation) {
                        message = `
                            <strong>⚠️ Testing Mode Active (Geofence Bypassed)</strong><br>
                            Nearest Office: ${nearestOffice ? nearestOffice.name : 'Unknown'}<br>
                            Distance: ${Math.round(minDistance)} meters (Max: ${nearestOffice ? nearestOffice.radius_meter : 0}m)<br>
                            <span class="text-xs text-indigo-600">You can submit attendance from anywhere.</span>
                        `;
                    }

                    locationStatus.innerHTML = message;
                    locationStatus.className = 'mb-4 p-3 bg-green-100 text-green-800 rounded border border-green-200';
                    attendanceForm.classList.remove('hidden');
                    checkReady();
                } else {
                    locationStatus.innerHTML = `
                        <strong>❌ You are too far!</strong><br>
                        Nearest Office: ${nearestOffice ? nearestOffice.name : 'Unknown'}<br>
                        Distance: ${Math.round(minDistance)} meters (Max: ${nearestOffice ? nearestOffice.radius_meter : 0}m)
                    `;
                    locationStatus.className = 'mb-4 p-3 bg-red-100 text-red-800 rounded border border-red-200';
                    attendanceForm.classList.add('hidden'); // Hide form if out of range logic is strictly applied client-side
                }

            }, error => {
                locationStatus.textContent = "Error getting location. Please allow GPS.";
                locationStatus.classList.add('bg-red-100', 'text-red-800');
            });
        } else {
            locationStatus.textContent = "Geolocation is not supported by this browser.";
        }

        // 4. Device Info
        deviceInput.value = navigator.userAgent;

        function checkReady() {
            if (photoInput.value && latInput.value) {
                submitBtn.disabled = false;
            }
        }
    </script>
</x-app-layout>