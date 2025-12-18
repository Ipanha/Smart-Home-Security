<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen p-8">

    <div class="max-w-4xl mx-auto">
        <a href="/admin/users" class="flex items-center gap-2 text-gray-500 hover:text-black mb-6 transition font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Users
        </a>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 mb-6 flex flex-col md:flex-row items-center gap-8">
            @if(isset($user['profile_pic']) && $user['profile_pic'])
                <img src="{{ $user['profile_pic'] }}" class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg">
            @else
                <div class="w-32 h-32 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-4xl font-bold shadow-lg">
                    {{ substr($user['name'] ?? 'U', 0, 1) }}
                </div>
            @endif

            <div class="text-center md:text-left">
                <h1 class="text-3xl font-bold text-gray-900">{{ $user['name'] }}</h1>
                <p class="text-gray-500 mt-1">{{ $user['email'] }}</p>
                <div class="flex gap-3 mt-4 justify-center md:justify-start">
                    <span class="px-4 py-1.5 bg-blue-50 text-blue-600 rounded-full text-sm font-bold uppercase tracking-wide">
                        {{ $user['role'] ?? 'Member' }}
                    </span>
                    <span class="px-4 py-1.5 bg-gray-100 text-gray-600 rounded-full text-sm font-medium">
                        Joined: {{ isset($user['created_at']) ? \Carbon\Carbon::parse($user['created_at'])->format('M d, Y') : 'Unknown' }}
                    </span>
                </div>
            </div>
        </div>

        @if(isset($homeData['home']))
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-2xl bg-orange-100 flex items-center justify-center text-orange-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Home Information</h2>
                    <p class="text-gray-500 text-sm">Residence details and family members</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-gray-50 rounded-2xl p-6">
                    <div class="text-sm text-gray-500 uppercase tracking-wider font-bold mb-1">House Name</div>
                    <div class="text-xl font-bold text-gray-900 mb-4">{{ $homeData['home']['name'] }}</div>
                    
                    <div class="text-sm text-gray-500 uppercase tracking-wider font-bold mb-1">Owner</div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center text-xs font-bold text-white">
                             {{ substr($homeData['owner']['name'] ?? '?', 0, 1) }}
                        </div>
                        <span class="font-medium text-gray-800">{{ $homeData['owner']['name'] ?? 'Unknown' }}</span>
                    </div>
                </div>

                <div>
                    <h3 class="font-bold text-gray-900 mb-4">Family Members</h3>
                    <div class="space-y-3">
                        @forelse($homeData['members'] as $member)
                            <div class="flex items-center justify-between p-3 bg-white border border-gray-100 rounded-xl hover:shadow-sm transition">
                                <div class="flex items-center gap-3">
                                    @if(isset($member['profile_pic']) && $member['profile_pic'])
                                        <img src="{{ $member['profile_pic'] }}" class="w-10 h-10 rounded-full object-cover">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold">
                                            {{ substr($member['name'], 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-gray-800 text-sm">{{ $member['name'] }}</div>
                                        <div class="text-xs text-gray-400">{{ $member['email'] }}</div>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-gray-300">MEMBER</span>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm italic">No other members in this home.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900">No Home Assigned</h3>
            <p class="text-gray-500 mt-2">This user is not currently assigned to any home.</p>
        </div>
        @endif

    </div>

</body>
</html>