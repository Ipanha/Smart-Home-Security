<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartGuard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; } 
        .modal-enter { opacity: 0; transform: scale(0.95); }
        .modal-enter-active { opacity: 1; transform: scale(1); transition: opacity 0.2s, transform 0.2s; }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800 h-screen flex overflow-hidden">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-slate-900 text-white flex flex-col shadow-xl">
        <div class="h-16 flex items-center px-6 border-b border-slate-800 bg-slate-900">
            <span class="font-bold text-xl tracking-wider">SmartGuard</span>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="/admin/users" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ $view_type == 'users' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}"><span>👤</span> Users</a>
            <a href="/admin/homes" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ $view_type == 'homes' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}"><span>🏠</span> Homes</a>
            <a href="/admin/devices" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ $view_type == 'devices' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}"><span>🔌</span> Devices</a>
        </nav>
        <div class="p-4 border-t border-slate-800">
            <form action="/admin/logout" method="POST">@csrf<button class="w-full flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg transition text-sm font-medium">Logout</button></form>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 shadow-sm">
            <h2 class="text-xl font-bold text-gray-800 capitalize">{{ $view_type }} Management</h2>
            <div class="flex items-center gap-4">
                @if($view_type == 'users')
                    <button onclick="openCreateUserModal()" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-md transition-all"><span>+</span> Create User</button>
                @endif
                <div class="text-sm text-gray-500">Administrator</div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8">
            @if(session('success')) <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg shadow-sm">{{ session('success') }}</div> @endif
            @if(session('error')) <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg shadow-sm">{{ session('error') }}</div> @endif

            <!-- 1. USERS VIEW -->
            @if($view_type == 'users')
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3 w-12 text-center">No</th>
                                <th class="px-6 py-3">User</th>
                                <th class="px-6 py-3">Role</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($users as $index => $user)
                            @php $deleteId = $user->user_id ?? $user->_id ?? $user->id ?? ''; @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-center text-gray-400 font-mono text-sm">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $user->name ?? 'Unknown' }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-bold {{ ($user->role ?? '') == 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">{{ $user->role ?? 'User' }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if(($user->role ?? '') !== 'admin' && !empty($deleteId))
                                        <button onclick="openHomeModal('{{ $deleteId }}', '{{ $user->email }}')" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium mr-3">Create Home</button>
                                                                            <button
                                            onclick="openEditUserModal(
                                                '{{ $deleteId }}',
                                                '{{ $user->name }}',
                                                '{{ $user->email }}'
                                            )"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                                        >
                                            ✏️ Edit
                                        </button>
                                        <button onclick="openDeleteModal('/admin/delete-user/{{ $deleteId }}', 'User: {{ $user->email }}')" class="text-red-600 hover:text-red-800 text-sm font-medium">🗑️ Delete</button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- 2. HOMES VIEW -->
            @if($view_type == 'homes')
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3 w-12 text-center">No</th>
                                <th class="px-6 py-3">Home Name</th>
                                <th class="px-6 py-3">Owner Details</th>
                                <th class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($homes as $index => $home)
                            @php 
                                $homeId = $home['id'] ?? $home['_id'] ?? ''; 
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-center text-gray-400 font-mono text-sm">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 font-bold text-gray-800">{{ $home['name'] }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $home['owner_name'] ?? 'Unknown' }}</div>
                                    <div class="text-sm text-gray-500">{{ $home['owner_email'] ?? $home['owner_id'] }}</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if(!empty($homeId))
                                    <button onclick="openDeleteModal('/admin/delete-home/{{ $homeId }}', 'Home: {{ $home['name'] }}')" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                                    @endif
                                </td>
                            </tr>
                            @empty <tr><td colspan="4" class="p-6 text-center text-gray-500">No homes found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- 3. DEVICES VIEW -->
            @if($view_type == 'devices')
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase text-xs">
                            <tr><th class="px-6 py-3">Device Name</th><th class="px-6 py-3">Type</th><th class="px-6 py-3">Status</th><th class="px-6 py-3 text-right">Actions</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($devices as $device)
                            @php $deviceId = $device['id'] ?? $device['_id'] ?? ''; @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-bold text-gray-800">{{ $device['name'] }}</td>
                                <td class="px-6 py-4 capitalize text-gray-600">{{ $device['type'] }}</td>
                                <td class="px-6 py-4"><span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">Active</span></td>
                                <td class="px-6 py-4 text-right">
                                    @if(!empty($deviceId))
                                    <button onclick="openDeleteModal('/admin/delete-device/{{ $deviceId }}', 'Device: {{ $device['name'] }}')" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                                    @endif
                                </td>
                            </tr>
                            @empty <tr><td colspan="4" class="p-6 text-center text-gray-500">No devices found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </main>
    </div>

    <!-- MODALS -->
    <!-- Create User -->
    <div id="createUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-xl shadow-2xl w-96">
            <h3 class="font-bold text-lg mb-4">Create New User</h3>
            <form action="/admin/create-user" method="POST">
                @csrf
                <div class="space-y-3">
                    <div><label class="block text-xs font-bold uppercase">Full Name</label><input type="text" name="name" class="w-full border p-2 rounded" required></div>
                    <div><label class="block text-xs font-bold uppercase">Email</label><input type="email" name="email" class="w-full border p-2 rounded" required></div>
                    <div><label class="block text-xs font-bold uppercase">Password</label><input type="password" name="password" class="w-full border p-2 rounded" required></div>
                    <div><label class="block text-xs font-bold uppercase">Confirm Password</label><input type="password" name="password_confirmation" class="w-full border p-2 rounded" required></div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closeModal('createUserModal')" class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded">Cancel</button>
                    <button class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded font-bold">Create User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Home -->
    <div id="homeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-xl shadow-2xl w-96">
            <h3 class="font-bold text-lg mb-4">Create Home</h3>
            <form action="/admin/create-home" method="POST">
                @csrf
                <input type="hidden" name="owner_id" id="modalOwnerId">
                <p class="text-sm text-gray-500 mb-2">Assigning to: <span id="modalOwnerEmail" class="font-bold"></span></p>
                <input type="text" name="name" class="w-full border p-2 rounded mb-4" placeholder="Home Name" required>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal('homeModal')" class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded">Cancel</button>
                    <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-bold">Create</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-xl shadow-2xl w-96">
        <h3 class="font-bold text-lg mb-4">Edit User</h3>

        <form id="editUserForm" method="POST">
            @csrf

            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-bold uppercase">Full Name</label>
                    <input type="text" name="name" id="editUserName" class="w-full border p-2 rounded" required>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase">Email</label>
                    <input type="email" name="email" id="editUserEmail" class="w-full border p-2 rounded" required>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase">
                        New Password <span class="text-gray-400">(optional)</span>
                    </label>
                    <input type="password" name="password" class="w-full border p-2 rounded">
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeModal('editUserModal')" class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded">
                    Cancel
                </button>
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded font-bold">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>


    <!-- Delete Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-xl shadow-2xl w-full max-w-sm border-t-4 border-red-500">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Are you sure?</h3>
            <p class="text-sm text-gray-500 mb-6">Delete <span id="deleteItemName" class="font-bold">this item</span>? This cannot be undone.</p>
            <form id="deleteForm" action="" method="POST" class="flex justify-end gap-3">
                @csrf
                <button type="button" onclick="closeModal('deleteModal')" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">Cancel</button>
                <button class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold">Yes, Delete It</button>
            </form>
        </div>
    </div>

    <script>
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); document.getElementById(id).classList.remove('flex'); }
        function openModal(id) { document.getElementById(id).classList.remove('hidden'); document.getElementById(id).classList.add('flex'); }
        function openCreateUserModal() { openModal('createUserModal'); }
        function openHomeModal(id, email) { openModal('homeModal'); document.getElementById('modalOwnerId').value = id; document.getElementById('modalOwnerEmail').innerText = email; }
        function openDeleteModal(actionUrl, itemName) { openModal('deleteModal'); document.getElementById('deleteForm').action = actionUrl; document.getElementById('deleteItemName').innerText = itemName; }
        function openEditUserModal(id, name, email) {
        openModal('editUserModal');

        document.getElementById('editUserName').value = name;
        document.getElementById('editUserEmail').value = email;

        document.getElementById('editUserForm').action =
            `/admin/update-user/${id}`;
    }

    </script>
</body>
</html>