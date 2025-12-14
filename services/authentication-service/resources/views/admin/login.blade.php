<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex justify-center items-center">
    <div class="bg-white p-8 rounded shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Smart Home Admin</h2>
        
        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-2 rounded mb-4">{{ $errors->first() }}</div>
        @endif

        <form action="/admin/login" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block mb-1">Email</label>
                <input type="email" name="email" value="admin@smarthome.com" class="w-full border p-2 rounded" required>
            </div>
            <div class="mb-6">
                <label class="block mb-1">Password</label>
                <input type="password" name="password" value="admin123" class="w-full border p-2 rounded" required>
            </div>
            <button class="w-full bg-blue-600 text-white py-2 rounded font-bold hover:bg-blue-700">Login</button>
        </form>
    </div>
</body>
</html>