<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Smart Home Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="relative min-h-screen overflow-hidden bg-black flex items-center justify-center">

    <!-- Animated background blobs -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-600/30 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute top-1/3 -right-32 w-96 h-96 bg-cyan-500/20 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-purple-600/20 rounded-full blur-3xl animate-pulse"></div>

    <!-- Login Card -->
    <div class="relative z-10 w-full max-w-md">

        <div class="bg-white/10 backdrop-blur-2xl border border-white/20 rounded-3xl shadow-[0_0_80px_rgba(59,130,246,0.35)] p-8">

            <!-- Logo -->
            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h5m4 0h5a1 1 0 001-1V10" />
                    </svg>
                </div>
            </div>

            <!-- Title -->
            <h2 class="text-center text-3xl font-bold text-white tracking-wide">
                Smart Home
            </h2>
            <p class="text-center text-sm text-gray-300 mt-1 mb-8">
                Admin Control Panel
            </p>

            <!-- Error -->
            @if($errors->any())
                <div class="mb-4 rounded-xl bg-red-500/20 border border-red-500/40 text-red-200 px-4 py-2 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Form -->
            <form action="/admin/login" method="POST" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label class="text-xs uppercase tracking-widest text-gray-300">
                        Email
                    </label>
                    <input
                        type="email"
                        name="email"
                        value="admin@smarthome.com"
                        required
                        class="mt-2 w-full rounded-xl bg-black/40 border border-white/20 px-4 py-3 text-white
                               placeholder-gray-400 focus:outline-none focus:border-cyan-400
                               focus:ring-2 focus:ring-cyan-400/40 transition"
                        placeholder="admin@smarthome.com"
                    >
                </div>

                <!-- Password -->
                <div>
                    <label class="text-xs uppercase tracking-widest text-gray-300">
                        Password
                    </label>
                    <input
                        type="password"
                        name="password"
                        value="admin123"
                        required
                        class="mt-2 w-full rounded-xl bg-black/40 border border-white/20 px-4 py-3 text-white
                               placeholder-gray-400 focus:outline-none focus:border-blue-500
                               focus:ring-2 focus:ring-blue-500/40 transition"
                        placeholder="••••••••"
                    >
                </div>

                <!-- Button -->
                <button
                    type="submit"
                    class="group relative w-full overflow-hidden rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 py-3 font-semibold text-white
                           shadow-lg transition-all duration-300 hover:shadow-cyan-500/40 hover:scale-[1.02]"
                >
                    <span class="relative z-10">Access Dashboard</span>
                    <span class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition"></span>
                </button>
            </form>

            <!-- Footer -->
            <p class="mt-6 text-center text-xs text-gray-400">
                Secure Smart Home System © 2025
            </p>

        </div>
    </div>

</body>
</html>
