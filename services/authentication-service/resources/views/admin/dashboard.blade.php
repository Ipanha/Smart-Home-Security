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
        
        /* Custom Scrollbar for sleek look */
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
            
            <a href="/admin/dashboard" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all group
               {{ $view_type == 'dashboard' ? 'bg-gradient-to-r from-blue-600 to-indigo-600 shadow-lg shadow-blue-500/30 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>

            <a href="/admin/users" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all group
               {{ $view_type == 'users' ? 'bg-gradient-to-r from-purple-600 to-indigo-600 shadow-lg shadow-purple-500/30 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="font-medium">Users</span>
            </a>

            <a href="/admin/homes" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all group
               {{ $view_type == 'homes' ? 'bg-gradient-to-r from-indigo-600 to-blue-600 shadow-lg text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="font-medium">Homes</span>
            </a>

            <a href="/admin/devices" 
               class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all group
               {{ $view_type == 'devices' ? 'bg-gradient-to-r from-indigo-600 to-blue-600 shadow-lg text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span class="font-medium">Devices</span>
            </a>

        </nav>
        
        <div class="p-4 border-t border-gray-800">
            <form action="/admin/logout" method="POST">
                @csrf
                <button class="w-full flex items-center justify-center gap-2 text-gray-400 hover:text-white transition py-2 text-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 bg-white">
        
        <header class="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-8">
            <button class="text-gray-600 hover:text-black opacity-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>

            <div class="w-10 h-10 rounded-full bg-black flex items-center justify-center text-white overflow-hidden cursor-pointer hover:ring-4 ring-gray-100 transition">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8">
            
            @if(session('success')) <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg shadow-sm">{{ session('success') }}</div> @endif
            @if(session('error')) <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg shadow-sm">{{ session('error') }}</div> @endif

            @if($view_type == 'dashboard')
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-black mb-6">Admin Dashboard</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        
                        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] hover:shadow-lg transition">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Users</p>
                                    <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ count($users) ?? '0' }}</h3>
                                </div>
                                <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] hover:shadow-lg transition">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Active Homes</p>
                                    <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ isset($homes) ? count($homes) : '0' }}</h3>
                                </div>
                                <div class="p-3 bg-purple-50 rounded-xl text-purple-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] hover:shadow-lg transition">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Devices</p>
                                    <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ isset($devices) ? count($devices) : '0' }}</h3>
                                </div>
                                <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            @endif

            @if($view_type == 'users')
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-black">Users Management</h2>
                    
                    <div class="flex gap-3 w-full md:w-auto">
                        <div class="relative w-full md:w-64">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input type="text" id="userSearch" onkeyup="filterUsers()" placeholder="Search users, homes..." 
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        </div>

                        <button onclick="openCreateUserModal()" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-indigo-200 transition-all">
                            <span>+</span> Create User
                        </button>
                    </div>
                </div>
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full text-left" id="usersTable">
                        <thead class="bg-gray-50 border-b border-gray-100 text-gray-400 uppercase text-xs font-semibold tracking-wider">
                            <tr>
                                <th class="px-6 py-4">User Profile</th>
                                <th class="px-6 py-4">Home</th>
                                <th class="px-6 py-4">Role</th>
                                <th class="px-6 py-4">Joined Date</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($users as $user)
                            @php 
                                $u = (object)$user; 
                                $userId = $u->user_id ?? $u->id ?? '';
                                $name = $u->name ?? 'Unknown';
                                $email = $u->email ?? 'No Email';
                                $role = $u->role ?? 'User';
                                $home = $u->home_name ?? 'N/A';
                                $date = $u->joined_date ?? 'N/A';
                                $pic = $u->profile_pic ?? null;
                                $currentHomeId = $u->home_id ?? '';
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors user-row">
                                <td class="px-6 py-4">
                                    <a href="/admin/users/{{ $userId }}" class="flex items-center gap-3 group">
                                        @if($pic)
                                            <img src="{{ $pic }}" class="w-10 h-10 rounded-full object-cover border border-gray-200 group-hover:ring-2 ring-indigo-500 transition">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-100 to-indigo-200 flex items-center justify-center text-indigo-600 font-bold group-hover:ring-2 ring-indigo-500 transition">
                                                {{ substr($name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-bold text-gray-900 group-hover:text-indigo-600 transition user-name">{{ $name }}</div>
                                            <div class="text-sm text-gray-400">{{ $email }}</div>
                                        </div>
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-gray-600 font-medium user-home">{{ $home }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide {{ $role == 'admin' ? 'bg-purple-100 text-purple-600' : 'bg-blue-50 text-blue-600' }}">
                                        {{ $role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $date }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button onclick="openEditUserModal('{{ $userId }}', '{{ $name }}', '{{ $email }}', '{{ $currentHomeId }}')" class="text-gray-400 hover:text-blue-600 mx-2 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    <button onclick="openDeleteModal('/admin/delete-user/{{ $userId }}', 'User: {{ $email }}')" class="text-gray-400 hover:text-red-600 mx-2 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if($view_type == 'homes')
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-black">Homes Management</h2>
                    
                    <div class="flex gap-3 w-full md:w-auto">
                        <div class="relative w-full md:w-64">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input type="text" id="homeSearch" onkeyup="filterHomes()" placeholder="Search home or owner..." 
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        </div>

                        <button onclick="openCreateHomeModal()" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-indigo-200 transition-all whitespace-nowrap">
                            <span>+</span> Create Home
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full text-left" id="homesTable">
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
                        @php 
                            $rawId = $home['id'] ?? $home['_id'] ?? '';
                            $homeId = is_array($rawId) ? ($rawId['$oid'] ?? '') : $rawId;
                            $homeName = $home['name'] ?? 'Unnamed';
                            $jsName = addslashes($homeName);
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors home-row">
                            <td class="px-6 py-4 text-center text-gray-400 text-sm">{{ $index + 1 }}</td>
                            
                            <td class="px-6 py-4 font-bold text-gray-800 home-name">{{ $homeName }}</td>
                            
                            <td class="px-6 py-4 text-gray-600 home-owner">{{ $home['owner_name'] ?? 'Unknown' }}</td>
                            
                            <td class="px-6 py-4 text-right flex justify-end gap-3">
                                @if(!empty($homeId))
                                    <button onclick="openEditHomeModal('{{ $homeId }}', '{{ $jsName }}')" class="text-amber-500 hover:text-amber-700 text-sm font-medium">Edit</button>
                                    <button onclick="openDeleteModal('/admin/delete-home/{{ $homeId }}', 'Home: {{ $jsName }}')" class="text-red-500 hover:text-red-700 text-sm font-medium">Delete</button>
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
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-black">Devices Registry</h2>
                    
                    <div class="flex gap-3 w-full md:w-auto">
                        <div class="relative w-full md:w-64">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input type="text" id="deviceSearch" onkeyup="filterDevices()" placeholder="Search device, home, or type..." 
                                class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        </div>

                        <button onclick="openCreateDeviceModal()" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-indigo-200 transition-all whitespace-nowrap">
                            <span>+</span> Add Device
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <table class="w-full text-left" id="devicesTable">
    <thead class="bg-gray-50 border-b border-gray-100 text-gray-400 uppercase text-xs font-semibold">
        <tr>
            <th class="px-6 py-4 w-16 text-center">No</th>
            <th class="px-6 py-4">Device Details</th>
            <th class="px-6 py-4">Home Location</th>
            <th class="px-6 py-4">Status</th>
            <th class="px-6 py-4">Created Date</th>
            <th class="px-6 py-4 text-right">Actions</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-50">
        @forelse($devices as $index => $device) @php 
            $deviceId = $device['id'] ?? $device['_id'] ?? '';
            $devName = addslashes($device['name']);
            $devType = $device['type'] ?? 'unknown';
            $devStatus = $device['status'] ?? 'active';
            $homeId = $device['home_id'] ?? ''; 
            $homeName = $device['home_name'] ?? 'Unknown';
            $created = isset($device['created_at']) ? \Carbon\Carbon::parse($device['created_at'])->format('M d, Y') : 'N/A';
        @endphp
        <tr class="hover:bg-gray-50 transition-colors device-row">
            
            <td class="px-6 py-4 text-center text-gray-400 text-sm font-medium">
                {{ $loop->iteration }}
            </td>

            <td class="px-6 py-4">
                <div class="font-bold text-gray-800 device-name">{{ $device['name'] }}</div>
                <div class="text-xs text-gray-400 uppercase tracking-wider device-type">{{ $devType }}</div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600 font-medium device-home">
                {{ $homeName }}
            </td>
            <td class="px-6 py-4">
                <span class="px-2 py-1 rounded-md text-xs font-bold uppercase tracking-wider 
                {{ $devStatus == 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600' }}">
                    {{ $devStatus }}
                </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">
                {{ $created }}
            </td>
            <td class="px-6 py-4 text-right flex justify-end gap-3">
                @if(!empty($deviceId))
                <button onclick="openEditDeviceModal('{{ $deviceId }}', '{{ $devName }}', '{{ $devType }}', '{{ $devStatus }}', '{{ $homeId }}')" class="text-amber-500 hover:text-amber-700 text-sm font-medium">Edit</button>
                <button onclick="openDeleteModal('/admin/delete-device/{{ $deviceId }}', 'Device: {{ $devName }}')" class="text-red-500 hover:text-red-700 text-sm font-medium">Delete</button>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="p-8 text-center text-gray-400">No devices online.</td></tr>
        @endforelse
    </tbody>
</table>
                </div>
            @endif

        </main>
    </div>

    <div id="createUserModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 transition-all">
        <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md transform scale-100 transition-all max-h-[90vh] overflow-y-auto">
            <h3 class="font-bold text-2xl mb-6 text-gray-900">Create New User</h3>
            
            <form action="/admin/create-user" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Profile Picture</label>
                    <input type="file" name="profile_pic" accept="image/*" class="w-full bg-gray-50 border border-gray-200 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 rounded-xl">
                </div>

                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Full Name</label><input type="text" name="name" class="w-full bg-gray-50 border-gray-200 border p-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" required></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email</label><input type="email" name="email" class="w-full bg-gray-50 border-gray-200 border p-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" required></div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Assign to Home (Optional)</label>
                    <select name="home_id" class="w-full bg-gray-50 border-gray-200 border p-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="" selected>-- No Home --</option>
                        @if(isset($homes))
                            @foreach($homes as $home)
                                @php $hid = $home['id'] ?? $home['_id'] ?? ($home['id']['$oid'] ?? ''); @endphp
                                <option value="{{ $hid }}">{{ $home['name'] }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Password</label><input type="password" name="password" class="w-full bg-gray-50 border-gray-200 border p-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" required></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Confirm Password</label><input type="password" name="password_confirmation" class="w-full bg-gray-50 border-gray-200 border p-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500" required></div>
                
                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" onclick="closeModal('createUserModal')" class="px-5 py-2.5 text-gray-500 hover:bg-gray-100 rounded-xl font-medium transition">Cancel</button>
                    <button class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-200 transition">Create Account</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editUserModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md">
            <h3 class="font-bold text-2xl mb-6">Edit User</h3>
            <form id="editUserForm" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Profile Picture</label>
                    <input type="file" name="profile_pic" accept="image/*" class="w-full bg-gray-50 border border-gray-200 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 rounded-xl">
                </div>

                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Full Name</label><input type="text" name="name" id="editUserName" class="w-full bg-gray-50 border-gray-200 border p-3 rounded-xl" required></div>
                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email</label><input type="email" name="email" id="editUserEmail" class="w-full bg-gray-50 border-gray-200 border p-3 rounded-xl" required></div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Update Home Assignment</label>
                    <select name="home_id" id="editUserHome" class="w-full bg-gray-50 border-gray-200 border p-3 rounded-xl">
                        <option value="">-- Don't Change --</option>
                        @if(isset($homes))
                            @foreach($homes as $home)
                                @php $hid = $home['id'] ?? $home['_id'] ?? ($home['id']['$oid'] ?? ''); @endphp
                                <option value="{{ $hid }}">{{ $home['name'] }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div><label class="block text-xs font-bold text-gray-500 uppercase mb-1">New Password (Optional)</label><input type="password" name="password" class="w-full bg-gray-50 border-gray-200 border p-3 rounded-xl"></div>
                
                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" onclick="closeModal('editUserModal')" class="px-5 py-2.5 text-gray-500 hover:bg-gray-100 rounded-xl font-medium">Cancel</button>
                    <button class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold shadow-lg shadow-blue-200">Save Changes</button>
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
                                    @php 
                                        $u = (object)$user; 
                                        $uid = $u->id ?? $u->_id ?? $u->user_id ?? '';
                                        $uname = $u->name ?? 'Unknown';
                                        $uemail = $u->email ?? '';
                                    @endphp
                                    @if($uid)
                                        <option value="{{ $uid }}">{{ $uname }} ({{ $uemail }})</option>
                                    @endif
                                @endforeach
                            @else
                                <option value="" disabled>No users found</option>
                            @endif
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                        </div>
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
            @method('PUT')
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
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mb-4 text-red-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Delete Item</h3>
            <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete <span id="deleteItemName" class="font-bold text-gray-800">this item</span>? This action cannot be undone.</p>
            <form id="deleteForm" action="" method="POST" class="flex justify-end gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeModal('deleteModal')" class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl font-medium">Cancel</button>
                <button class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold shadow-lg shadow-red-200">Yes, Delete</button>
            </form>
        </div>
    </div>
<div id="createDeviceModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md">
            <h3 class="font-bold text-2xl mb-6 text-gray-900">Add New Device</h3>
            <form action="/admin/create-device" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Select Home</label>
                    <select name="home_id" class="w-full bg-gray-50 border-gray-200 border p-3 rounded-xl" required>
                        <option value="" disabled selected>Select a Home...</option>
                        @if(isset($homes))
                            @foreach($homes as $home)
                                @php $hid = $home['id'] ?? $home['_id'] ?? ($home['id']['$oid'] ?? ''); @endphp
                                <option value="{{ $hid }}">{{ $home['name'] }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Device Name</label>
                    <input type="text" name="name" class="w-full bg-gray-50 border-gray-200 border p-3 rounded-xl" placeholder="e.g. Living Room Cam" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Type</label>
                        <select name="type" class="w-full bg-gray-50 border-gray-200 border p-3 rounded-xl">
                            <option value="camera">Camera</option>
                            <option value="sensor">Sensor</option>
                            <option value="lock">Lock</option>
                            <option value="light">Light</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
                        <select name="status" class="w-full bg-gray-50 border-gray-200 border p-3 rounded-xl">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" onclick="closeModal('createDeviceModal')" class="px-5 py-2 text-gray-500">Cancel</button>
                    <button class="px-5 py-2 bg-indigo-600 text-white rounded-xl font-bold">Add Device</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editDeviceModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50">
        <div class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md">
            <h3 class="font-bold text-2xl mb-6 text-gray-900">Edit Device</h3>
            <form id="editDeviceForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Assign to Home</label>
                    <select name="home_id" id="editDeviceHome" class="w-full bg-gray-50 border-gray-200 border p-3 rounded-xl" required>
                        <option value="" disabled>Select a Home...</option>
                        @if(isset($homes))
                            @foreach($homes as $home)
                                @php $hid = $home['id'] ?? $home['_id'] ?? ($home['id']['$oid'] ?? ''); @endphp
                                <option value="{{ $hid }}">{{ $home['name'] }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Device Name</label>
                    <input type="text" name="name" id="editDeviceName" class="w-full bg-gray-50 border-gray-200 border p-3 rounded-xl" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Type</label>
                        <select name="type" id="editDeviceType" class="w-full bg-gray-50 border-gray-200 border p-3 rounded-xl">
                            <option value="camera">Camera</option>
                            <option value="sensor">Sensor</option>
                            <option value="lock">Lock</option>
                            <option value="light">Light</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
                        <select name="status" id="editDeviceStatus" class="w-full bg-gray-50 border-gray-200 border p-3 rounded-xl">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-8">
                    <button type="button" onclick="closeModal('editDeviceModal')" class="px-5 py-2 text-gray-500">Cancel</button>
                    <button class="px-5 py-2 bg-amber-500 text-white rounded-xl font-bold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    <script>
    function closeModal(id) { 
        const modal = document.getElementById(id);
        if(modal) {
            modal.classList.add('hidden'); 
            modal.classList.remove('flex'); 
        }
    }
    
    function openModal(id) { 
        const modal = document.getElementById(id);
        if(modal) {
            modal.classList.remove('hidden'); 
            modal.classList.add('flex'); 
        }
    }

    // Modal Openers
    function openCreateUserModal() { openModal('createUserModal'); }
    function openCreateHomeModal() { openModal('createHomeModal'); }
    
    function openEditUserModal(id, name, email, homeId) {
        openModal('editUserModal');
        document.getElementById('editUserName').value = name;
        document.getElementById('editUserEmail').value = email;
        
        // Set the current home in the dropdown
        if(homeId) {
            document.getElementById('editUserHome').value = homeId;
        } else {
            document.getElementById('editUserHome').value = "";
        }

        document.getElementById('editUserForm').action = '/admin/update-user/' + id; 
    }
    function filterUsers() {
        let input = document.getElementById('userSearch');
        let filter = input.value.toUpperCase();
        let table = document.getElementById('usersTable');
        let tr = table.getElementsByTagName('tr');

        for (let i = 1; i < tr.length; i++) {
            let nameCol = tr[i].getElementsByClassName('user-name')[0];
            let homeCol = tr[i].getElementsByClassName('user-home')[0];

            if (nameCol && homeCol) {
                let txtName = nameCol.textContent || nameCol.innerText;
                let txtHome = homeCol.textContent || homeCol.innerText;

                if (txtName.toUpperCase().indexOf(filter) > -1 || txtHome.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }

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
    function openCreateDeviceModal() {
        openModal('createDeviceModal');
    }

    function openEditDeviceModal(id, name, type, status, homeId) {
        openModal('editDeviceModal');
        
        // Populate fields
        document.getElementById('editDeviceName').value = name;
        document.getElementById('editDeviceType').value = type;
        document.getElementById('editDeviceStatus').value = status;
        
        // Select the correct Home
        if(homeId) {
            document.getElementById('editDeviceHome').value = homeId;
        }

        // Set form action
        document.getElementById('editDeviceForm').action = '/admin/update-device/' + id;
    }

    function filterHomes() {
        // 1. Get input value
        let input = document.getElementById('homeSearch');
        let filter = input.value.toUpperCase();

        // 2. Get table and rows
        let table = document.getElementById('homesTable');
        let tr = table.getElementsByTagName('tr');

        // 3. Loop through rows (start at 1 to skip header)
        for (let i = 1; i < tr.length; i++) {
            // Get searchable elements within the row
            let nameCol = tr[i].getElementsByClassName('home-name')[0];
            let ownerCol = tr[i].getElementsByClassName('home-owner')[0];

            if (nameCol && ownerCol) {
                let txtName = nameCol.textContent || nameCol.innerText;
                let txtOwner = ownerCol.textContent || ownerCol.innerText;

                // Check if name OR owner matches the search term
                if (txtName.toUpperCase().indexOf(filter) > -1 || 
                    txtOwner.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }

    function filterDevices() {
        // 1. Get input value
        let input = document.getElementById('deviceSearch');
        let filter = input.value.toUpperCase();
        
        // 2. Get table and rows
        let table = document.getElementById('devicesTable');
        let tr = table.getElementsByTagName('tr');

        // 3. Loop through rows (skip header)
        for (let i = 1; i < tr.length; i++) {
            // Get searchable elements within the row
            let nameCol = tr[i].getElementsByClassName('device-name')[0];
            let typeCol = tr[i].getElementsByClassName('device-type')[0];
            let homeCol = tr[i].getElementsByClassName('device-home')[0];

            if (nameCol && typeCol && homeCol) {
                let txtName = nameCol.textContent || nameCol.innerText;
                let txtType = typeCol.textContent || typeCol.innerText;
                let txtHome = homeCol.textContent || homeCol.innerText;

                // Check if any column matches the search term
                if (txtName.toUpperCase().indexOf(filter) > -1 || 
                    txtType.toUpperCase().indexOf(filter) > -1 || 
                    txtHome.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
</script>
</body>
</html>