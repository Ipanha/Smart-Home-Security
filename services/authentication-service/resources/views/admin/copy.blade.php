<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Home - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Poppins', sans-serif; } 
        .modal-enter { opacity: 0; transform: scale(0.95); }
        .modal-enter-active { opacity: 1; transform: scale(1); transition: opacity 0.2s, transform 0.2s; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-white font-sans text-gray-800 h-screen flex overflow-hidden">

    <aside class="w-64 bg-[#1A1C23] text-white flex flex-col flex-shrink-0 transition-all duration-300">
        <div class="h-20 flex items-center gap-3 px-6 border-b border-gray-800">
            <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center p-1">
                <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M40 90 L100 30 L160 90" stroke="#DC2626" stroke-width="20" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M140 50 V30 H160 V70" fill="#DC2626"/>
                    <path d="M60 90 V150 C60 160 140 160 140 150 V90" stroke="#1E3A8A" stroke-width="15" fill="none"/>
                    <path d="M75 90 L100 65 L125 90" stroke="#1E3A8A" stroke-width="12" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <span class="font-bold text-xl tracking-wide text-white">Smart Home</span>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-3">
            <a href="/admin/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all group {{ $view_type == 'dashboard' ? 'bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="font-medium">Dashboard</span>
            </a>
            <a href="/admin/users" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all group {{ $view_type == 'users' ? 'bg-gradient-to-r from-purple-600 to-indigo-600 shadow-lg text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                <span class="font-medium">Users</span>
            </a>
            <a href="/admin/homes" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all group {{ $view_type == 'homes' ? 'bg-gradient-to-r from-indigo-600 to-blue-600 shadow-lg text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                <span class="font-medium">Homes</span>
            </a>
            <a href="/admin/devices" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all group {{ $view_type == 'devices' ? 'bg-gradient-to-r from-indigo-600 to-blue-600 shadow-lg text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                <span class="font-medium">Devices</span>
            </a>
        </nav>
        
        <div class="p-4 border-t border-gray-800">
            <form action="/admin/logout" method="POST">
                @csrf
                <button class="w-full flex items-center justify-center gap-2 text-gray-400 hover:text-white transition py-2 text-sm">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 bg-white">
        <header class="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-8">
            <h1 class="text-xl font-bold text-gray-800 capitalize">{{ $view_type }}</h1>
            <div class="w-10 h-10 rounded-full bg-black flex items-center justify-center text-white overflow-hidden">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8">
            @if(session('success')) <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg shadow-sm">{{ session('success') }}</div> @endif
            @if(session('error')) <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg shadow-sm">{{ session('error') }}</div> @endif

            @if($view_type == 'dashboard')
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-lg transition">
                        <p class="text-xs font-medium text-gray-500 uppercase">Total Users</p>
                        <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ count($users) ?? '0' }}</h3>
                    </div>
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-lg transition">
                        <p class="text-xs font-medium text-gray-500 uppercase">Active Homes</p>
                        <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ count($homes) ?? '0' }}</h3>
                    </div>
                </div>
            @endif

            @if($view_type == 'users')
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-black">Users Management</h2>
                    <button onclick="openCreateUserModal()" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-indigo-200 transition-all">
                        <span>+</span> Create User
                    </button>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-gray-400 uppercase text-xs font-semibold">
                            <tr><th class="px-6 py-4">Name</th><th class="px-6 py-4">Email</th><th class="px-6 py-4 text-right">Actions</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-bold">{{ $user->name }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button onclick="openDeleteModal('/admin/delete-user/{{ $user->user_id }}', 'User: {{ $user->email }}')" class="text-red-500 hover:text-red-700 text-sm font-medium">Delete</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if($view_type == 'homes')
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-black">Homes Management</h2>
                    <button onclick="openCreateHomeModal()" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-indigo-200 transition-all">
                        <span>+</span> Create Home
                    </button>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100 text-gray-400 uppercase text-xs font-semibold">
                            <tr>
                                <th class="px-6 py-4 w-16 text-center">#</th>
                                <th class="px-6 py-4">Home Name</th>
                                <th class="px-6 py-4">Owner</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($homes as $index => $home)
                            @php $homeId = $home['id'] ?? $home['_id'] ?? ''; @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center text-gray-400 text-sm">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 font-bold text-gray-800">{{ $home['name'] }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $home['owner_name'] ?? 'Unknown' }}</td>
                                <td class="px-6 py-4 text-right flex justify-end gap-3">
                                    @if(!empty($homeId))
                                        <button onclick="openEditHomeModal('{{ $homeId }}', '{{ $home['name'] }}')" class="text-amber-500 hover:text-amber-700 text-sm font-medium">Edit</button>
                                        <button onclick="openDeleteModal('/admin/delete-home/{{ $homeId }}', 'Home: {{ $home['name'] }}')" class="text-red-500 hover:text-red-700 text-sm font-medium">Delete</button>
                                    @endif
                                </td>
                            </tr>
                            @empty 
                            <tr><td colspan="4" class="p-8 text-center text-gray-400">No homes found in system.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            @if($view_type == 'devices')
                <div class="mb-6"><h2 class="text-2xl font-bold text-black">Devices Registry</h2></div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100 text-gray-400 uppercase text-xs font-semibold">
                            <tr><th class="px-6 py-4">Device Name</th><th class="px-6 py-4">Type</th><th class="px-6 py-4 text-right">Actions</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($devices as $device)
                            @php $deviceId = $device['id'] ?? $device['_id'] ?? ''; @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-800">{{ $device['name'] }}</td>
                                <td class="px-6 py-4 capitalize text-gray-500">{{ $device['type'] }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button onclick="openDeleteModal('/admin/delete-device/{{ $deviceId }}', 'Device: {{ $device['name'] }}')" class="text-red-500 hover:text-red-700 text-sm font-medium">Delete</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="p-8 text-center text-gray-400">No devices online.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

        </main>
    </div>

    <div id="createUserModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md">
            <h3 class="font-bold text-2xl mb-6">Create New User</h3>
            <form action="/admin/create-user" method="POST" class="space-y-4">
                @csrf
                <input type="text" name="name" placeholder="Full Name" class="w-full bg-gray-50 border p-3 rounded-xl" required>
                <input type="email" name="email" placeholder="Email" class="w-full bg-gray-50 border p-3 rounded-xl" required>
                <input type="password" name="password" placeholder="Password" class="w-full bg-gray-50 border p-3 rounded-xl" required>
                <input type="password" name="password_confirmation" placeholder="Confirm Password" class="w-full bg-gray-50 border p-3 rounded-xl" required>
                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" onclick="closeModal('createUserModal')" class="px-5 py-2 text-gray-500">Cancel</button>
                    <button class="px-5 py-2 bg-indigo-600 text-white rounded-xl font-bold">Create</button>
                </div>
            </form>
        </div>
    </div>

    <div id="createHomeModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md">
            <h3 class="font-bold text-2xl mb-6 text-gray-900">Add New Home</h3>
            <form action="/admin/create-home" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Home Name</label>
                    <input type="text" name="name" class="w-full bg-gray-50 border-gray-200 border p-3 rounded-xl" placeholder="e.g. My Apartment" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Assign Owner</label>
                    <div class="relative">
                        <select name="owner_id" class="w-full bg-gray-50 border-gray-200 border p-3 rounded-xl appearance-none" required>
                            <option value="" disabled selected>Select a User...</option>
                            @if(isset($users) && count($users) > 0)
                                @foreach($users as $user)
                                    @php $uid = $user['id'] ?? $user['_id'] ?? ''; @endphp
                                    <option value="{{ $uid }}">{{ $user['name'] }} ({{ $user['email'] }})</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" onclick="closeModal('createHomeModal')" class="px-5 py-2 text-gray-500">Cancel</button>
                    <button class="px-5 py-2 bg-indigo-600 text-white rounded-xl font-bold">Create Home</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editHomeModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md">
            <h3 class="font-bold text-2xl mb-6 text-gray-900">Edit Home</h3>
            <form id="editHomeForm" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Home Name</label>
                    <input type="text" name="name" id="editHomeName" class="w-full bg-gray-50 border-gray-200 border p-3 rounded-xl" required>
                </div>
                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" onclick="closeModal('editHomeModal')" class="px-5 py-2 text-gray-500">Cancel</button>
                    <button class="px-5 py-2 bg-amber-500 text-white rounded-xl font-bold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-3xl shadow-2xl w-full max-w-sm">
            <h3 class="text-xl font-bold text-gray-900 mb-2">Delete Item</h3>
            <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete <span id="deleteItemName" class="font-bold text-gray-800">this item</span>?</p>
            <form id="deleteForm" action="" method="POST" class="flex justify-end gap-3">
                @csrf
                <button type="button" onclick="closeModal('deleteModal')" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-xl">Cancel</button>
                <button class="px-4 py-2 bg-red-600 text-white rounded-xl font-bold">Yes, Delete</button>
            </form>
        </div>
    </div>

    <script>
        function closeModal(id) { 
            document.getElementById(id).classList.add('hidden'); 
            document.getElementById(id).classList.remove('flex'); 
        }
        function openModal(id) { 
            document.getElementById(id).classList.remove('hidden'); 
            document.getElementById(id).classList.add('flex'); 
        }
        function openCreateUserModal() { openModal('createUserModal'); }
        function openCreateHomeModal() { openModal('createHomeModal'); }
        
        function openEditHomeModal(id, name) {
            openModal('editHomeModal');
            document.getElementById('editHomeName').value = name;
            document.getElementById('editHomeForm').action = '/admin/update-home/' + id;
        }

        function openDeleteModal(actionUrl, itemName) { 
            openModal('deleteModal'); 
            document.getElementById('deleteForm').action = actionUrl; 
            document.getElementById('deleteItemName').innerText = itemName; 
        }
    </script>
</body>
</html>