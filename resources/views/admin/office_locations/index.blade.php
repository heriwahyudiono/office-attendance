<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Office Locations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                        {{ session('success') }}
                    </div>
                    @endif

                    <div class="flex flex-col md:flex-row gap-8">
                        <!-- List -->
                        <div class="md:w-2/3">
                            <h3 class="text-lg font-bold mb-4">Locations List</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Coordinates</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Radius</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($locations as $location)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $location->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                                {{ $location->latitude }}, {{ $location->longitude }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $location->radius_meter }} m</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">No locations found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Add Form -->
                        <div class="md:w-1/3">
                            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                                <h3 class="text-lg font-bold mb-4">Add New Location</h3>
                                <form action="{{ route('admin.office-locations.store') }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                                        <input type="text" name="name" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Latitude</label>
                                        <input type="text" name="latitude" class="w-full border-gray-300 rounded-md shadow-sm" required placeholder="-6.123456">
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Longitude</label>
                                        <input type="text" name="longitude" class="w-full border-gray-300 rounded-md shadow-sm" required placeholder="106.123456">
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Radius (meter)</label>
                                        <input type="number" name="radius_meter" class="w-full border-gray-300 rounded-md shadow-sm" required value="50">
                                    </div>
                                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">Add Location</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>