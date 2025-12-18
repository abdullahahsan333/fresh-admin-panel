<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
    <body class="min-h-screen bg-gray-100 flex items-center justify-center">
        <div class="w-full grid grid-cols-[60%_35%] gap-1">
            <div class="flex justify-center items-center">
                <div class="pl-7 w-full">
                    <img src="{{ asset('img/welpaper.webp') }}" alt="Logo" class="mx-auto mb-4">
                </div>
            </div>
            <div class="flex justify-center items-center">
                <div class="w-full">
                    <form method="POST" action="{{ route('store') }}" class="bg-white shadow rounded p-6 w-full max-w-sm">
                        @csrf
                        <h1 class="text-xl font-semibold mb-4">Create account</h1>
                        <label class="block text-sm mb-1">Name</label>
                        <input type="text" name="name" class="w-full border rounded px-3 py-2 mb-3" required>

                        <label class="block text-sm mb-1">Phone</label>
                        <input type="text" name="phone" class="w-full border rounded px-3 py-2 mb-3">

                        <label class="block text-sm mb-1">Email</label>
                        <input type="email" name="email" class="w-full border rounded px-3 py-2 mb-3" required>

                        <label class="block text-sm mb-1">Password</label>
                        <input type="password" name="password" class="w-full border rounded px-3 py-2 mb-3" required>
                        
                        <label class="block text-sm mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2 mb-4" required>
                        
                        <button class="w-full bg-gray-800 text-white rounded py-2">Register</button>
                        <p class="text-xs text-gray-500 mt-4">Have an account? <a href="{{ url('/') }}" class="underline">Login</a></p>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
