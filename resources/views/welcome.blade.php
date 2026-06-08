<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gray-100 text-gray-900">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-2xl mx-auto p-6 bg-white rounded-xl shadow-lg">
            <h1 class="text-3xl font-bold mb-4">Welcome to School Management System</h1>
            <p class="text-gray-600">Please login or register to continue.</p>
            <div class="mt-6 flex gap-4">
                <a href="{{ route('login') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Login</a>
                <a href="{{ route('register') }}" class="px-4 py-2 bg-gray-200 text-gray-900 rounded">Register</a>
            </div>
        </div>
    </div>
</body>

</html>
