<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage User Homes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; }
        .modal-enter { opacity: 0; transform: scale(0.95); }
        .modal-enter-active { opacity: 1; transform: scale(1); transition: opacity 0.2s, transform 0.2s; }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800 min-h-screen p-8">

    <div class="max-w-6xl mx-auto">
        
        <!-- HEADER -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <a href="/admin/users" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm flex items-center gap-1 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to User List
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Manage Homes</h1>
                <p class="text-gray-500">Managing properties for User ID: <span class="font-mono bg-gray-200 px-1 rounded text-xs">{{ $userId }}</span></p>
            </div>
            <button onclick="openCreateModal()" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg font-bold shadow-md transition-all flex items-center gap-2">
                <span>+</span> Add Home
            </button>
        </div>

        <!-- ALERTS -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg shadow-sm flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- HOMES GRID -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4">Home Name</th>
                        <th class="px-6 py-4">Home ID</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($homes as $home)
                    @php $homeId = $home['id'] ?? $home['_id'] ?? ''; @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                </div>
                                <span class="font-bold text-gray-800">{{ $home['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-gray-500">{{ $homeId }}</td>
                        <td class="px-6 py-4 text-right flex justify-end gap-3">
                            
                            <!-- Drill down to Devices -->
                            <a href="/admin/homes/{{ $homeId }}/devices" class="text-sm font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-md transition-colors border border-blue-200 flex items-center gap-1">
                                <span>🔌</span> Devices
                            </a>

                            <!-- Delete -->
                            <button onclick="openDeleteModal('/admin/delete-home/{{ $homeId }}', '{{ $home['name'] }}')" class="text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-md transition-colors border border-red-200 flex items-center gap-1">
                                <span>🗑️</span> Delete
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="p-8 text-center text-gray-500 bg-gray-50">
                            No homes assigned to this user yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- CREATE MODAL -->
    <div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-white p-6 rounded-xl shadow-2xl w-96 transform transition-all scale-100">
            <h3 class="font-bold text-lg mb-4 text-gray-800">Add New Home</h3>
            <form action="/admin/create-home" method="POST">
                @csrf
                <input type="hidden" name="owner_id" value="{{ $userId }}">
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Home Name</label>
                    <input type="text" name="name" class="w-full border p-2 rounded focus:ring-2 focus:ring-green-500 outline-none" placeholder="e.g. Vacation House" required>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded">Cancel</button>
                    <button class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded font-bold">Create</button>
                </div>
            </form>
        </div>
    </div>

    <!-- DELETE MODAL -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-white p-6 rounded-xl shadow-2xl w-full max-w-sm border-t-4 border-red-500">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Confirm Deletion</h3>
            <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete <span id="deleteItemName" class="font-bold text-gray-800">this home</span>?</p>
            <form id="deleteForm" action="" method="POST" class="flex justify-end gap-3">
                @csrf
                <button type="button" onclick="closeModal('deleteModal')" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">Cancel</button>
                <button class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold">Delete It</button>
            </form>
        </div>
    </div>

    <script>
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); document.getElementById(id).classList.remove('flex'); }
        function openCreateModal() { document.getElementById('createModal').classList.remove('hidden'); document.getElementById('createModal').classList.add('flex'); }
        function openDeleteModal(actionUrl, itemName) { 
            document.getElementById('deleteModal').classList.remove('hidden'); 
            document.getElementById('deleteModal').classList.add('flex');
            document.getElementById('deleteForm').action = actionUrl; 
            document.getElementById('deleteItemName').innerText = itemName; 
        }
    </script>
</body>
</html>