<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Devices</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 text-gray-800 p-8">
    
    <div class="max-w-5xl mx-auto">
        <a href="javascript:history.back()" class="text-indigo-600 hover:underline mb-4 inline-block">← Back to Homes</a>
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Devices</h1>
        </div>

        <!-- Add Device Form -->
        <div class="bg-white p-4 rounded shadow mb-6 border">
            <h3 class="font-bold mb-3 text-sm uppercase text-gray-500">Add New Device</h3>
            <form action="/admin/devices/create" method="POST" class="flex gap-2">
                @csrf
                <input type="hidden" name="home_id" value="{{ $homeId }}">
                <input type="text" name="name" placeholder="Device Name (e.g. Camera)" class="border p-2 rounded flex-1" required>
                <select name="type" class="border p-2 rounded">
                    <option value="camera">Camera</option>
                    <option value="sensor">Sensor</option>
                    <option value="lock">Door Lock</option>
                    <option value="light">Light</option>
                </select>
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 font-bold">Add Device</button>
            </form>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-lg shadow border overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="p-4">Device Name</th>
                        <th class="p-4">Type</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($devices as $device)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4 font-bold">{{ $device['name'] }}</td>
                        <td class="p-4 capitalize text-gray-600">{{ $device['type'] }}</td>
                        <td class="p-4"><span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-bold">{{ $device['status'] }}</span></td>
                        <td class="p-4 text-right">
                            <form action="/admin/devices/{{ $device['_id'] }}/delete" method="POST" onsubmit="return confirm('Delete this device?');">
                                @csrf
                                <button class="bg-red-50 text-red-700 px-3 py-1 rounded text-sm border border-red-200">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="p-4 text-center text-gray-500">No devices found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>