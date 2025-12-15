<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Smart Home - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">

    <nav class="w-full bg-white shadow-sm border-b border-gray-200 px-6 py-4 flex items-center">
        <div class="flex items-center gap-3">
             <svg width="40" height="40" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M40 90 L100 30 L160 90" stroke="#DC2626" stroke-width="15" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M140 50 V30 H160 V70" fill="#DC2626"/>
                <path d="M60 90 V150 C60 160 140 160 140 150 V90" stroke="#1E3A8A" stroke-width="12" fill="none"/>
                <path d="M75 90 L100 65 L125 90" stroke="#1E3A8A" stroke-width="10" stroke-linecap="round" stroke-linejoin="round"/>
                <rect x="92" y="110" width="16" height="16" fill="#1E3A8A"/>
                <path d="M30 170 C60 150 140 150 170 170" stroke="#1E3A8A" stroke-width="5" stroke-linecap="round"/>
           </svg>
           <h1 class="text-2xl font-bold text-black tracking-tight">Smart Home</h1>
        </div>
    </nav>

    <div class="flex-grow flex items-center justify-center p-4">

        <div class="bg-white rounded-[30px] shadow-2xl w-full max-w-5xl overflow-hidden min-h-[600px] flex flex-col md:flex-row border border-gray-100">

            <div class="w-full md:w-1/2 p-8 md:p-12 lg:p-16 flex flex-col justify-center relative border-r border-gray-100">
                
                <h2 class="text-4xl font-bold text-gray-900 text-center mb-10">Login</h2>

                @if($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r" role="alert">
                        <p class="font-bold">Error</p>
                        <p>{{ $errors->first() }}</p>
                    </div>
                @endif

                <form action="/admin/login" method="POST" class="space-y-8">
                    @csrf

                    <div class="relative group">
                        <label class="block text-gray-600 text-sm mb-1">Username</label>
                        <div class="relative">
                            <input 
                                type="email" 
                                name="email" 
                                value="admin@smarthome.com" 
                                required
                                class="w-full py-2 border-b-2 border-gray-300 text-gray-900 focus:outline-none focus:border-black transition-colors bg-transparent pr-10"
                            >
                            <div class="absolute right-0 top-1 text-black">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                    <path fill-rule="evenodd" d="M18.685 19.097A9.723 9.723 0 0021.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 003.065 7.097A9.716 9.716 0 0012 21.75a9.716 9.716 0 006.685-2.653zm-12.54-1.285A7.486 7.486 0 0112 15a7.486 7.486 0 015.855 2.812A8.224 8.224 0 0112 20.25a8.224 8.224 0 01-5.855-2.438zM15.75 9a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="relative group">
                        <label class="block text-gray-600 text-sm mb-1">Password</label>
                        <div class="relative">
                            <input 
                                type="password" 
                                name="password" 
                                value="admin123"
                                required
                                class="w-full py-2 border-b-2 border-gray-300 text-gray-900 focus:outline-none focus:border-black transition-colors bg-transparent pr-10"
                            >
                            <div class="absolute right-0 top-1 text-black">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                    <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 00-5.25 5.25v3a3 3 0 00-3 3v6.75a3 3 0 003 3h10.5a3 3 0 003-3v-6.75a3 3 0 00-3-3v-3c0-2.9-2.35-5.25-5.25-5.25zm3.75 8.25v-3a3.75 3.75 0 10-7.5 0v3h7.5z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded border-2">
                            <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                                Remember me
                            </label>
                        </div>

                        <div class="text-sm">
                            <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                                Forgot your password?
                            </a>
                        </div>
                    </div>

                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-gradient-to-r from-[#5356FF] to-[#8c52ff] hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform transition hover:scale-[1.01]">
                        Login
                    </button>
                </form>
            </div>

            <div class="hidden md:flex md:w-1/2 bg-white flex-col items-center justify-center p-8 relative">
                
                <div class="mb-8 transform hover:scale-105 transition duration-500">
                   <svg width="250" height="250" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M40 90 L100 30 L160 90" stroke="#DC2626" stroke-width="12" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M140 50 V30 H160 V70" fill="#DC2626"/>
                        <path d="M60 90 V150 C60 160 140 160 140 150 V90" stroke="#1E3A8A" stroke-width="10" fill="none"/>
                        <path d="M75 90 L100 65 L125 90" stroke="#1E3A8A" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                        <rect x="92" y="110" width="16" height="16" fill="#1E3A8A"/>
                        <path d="M30 170 C60 150 140 150 170 170" stroke="#1E3A8A" stroke-width="4" stroke-linecap="round"/>
                   </svg>
                </div>

                <div class="text-center">
                    <h2 class="text-3xl font-bold text-[#4338ca] mb-2">Welcome to</h2>
                    <h2 class="text-3xl font-bold text-[#4338ca]">Smart Home Security</h2>
                </div>
            </div>

        </div>
    </div>
</body>
</html>