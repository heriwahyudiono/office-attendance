<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Blocked Devices') }}
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
                            <h3 class="text-lg font-bold mb-4">Device Blacklist</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 border">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Device Info</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($devices as $device)
                                        <tr>
                                            <td class="px-6 py-4 text-xs font-mono break-all">{{ $device->device_info }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $device->reason ?? '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                                <form action="{{ route('admin.blocked-devices.destroy', $device->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to unblock this device?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Unblock</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">No blocked devices found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $devices->links() }}
                            </div>
                        </div>

                        <!-- Add Form -->
                        <div class="md:w-1/3">
                            <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                                <h3 class="text-lg font-bold mb-4">Block New Device</h3>
                                <form action="{{ route('admin.blocked-devices.store') }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Device Info String</label>
                                        <textarea name="device_info" class="w-full border-gray-300 rounded-md shadow-sm h-32" required placeholder="Paste user agent or device identifier here..."></textarea>
                                    </div>
                                    <div class="mb-4">
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Reason</label>
                                        <input type="text" name="reason" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="e.g. Suspicious activity">
                                    </div>
                                    <button type="submit" class="w-full bg-red-600 text-white font-bold py-2 px-4 rounded hover:bg-red-700">Block Device</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>