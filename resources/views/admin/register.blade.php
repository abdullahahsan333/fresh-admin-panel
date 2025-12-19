<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Register</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center">
    <div class="w-full grid grid-cols-[60%_40%] overflow-hidden gap-1">
        <div class="flex justify-center items-center">
            <div class="pl-7 w-full">
                <img src="{{ asset('img/welcome.jpg') }}" alt="Logo" class="mx-auto mb-4">
            </div>
        </div>

        <div class="flex justify-center items-center">
            <div class="w-full">
                <form method="POST" action="{{ route('admin.store') }}" class="bg-white shadow rounded p-6 w-full max-w-sm">
                    @csrf
                    <h1 class="text-2xl font-semibold mb-2">Adventure starts here 🚀</h1>
                    <p class="text-sm text-gray-500 mb-4">Make your app management easy and fun!</p>
                    <label class="block text-sm mb-1">Username <span class="text-red-500">*</span></label>
                    <input type="text" name="name" class="w-full border rounded px-3 py-2 mb-3" required>

                    <label class="block text-sm mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" class="w-full border rounded px-3 py-2 mb-3" required>

                    <label class="block text-sm mb-1">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" class="w-full border rounded px-3 py-2 mb-3" required>

                    <label class="block text-sm mb-1">Confirm Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2 mb-4" required>

                    <label class="inline-block text-sm mb-3 cursor-pointer">
                        <input type="checkbox" name="terms" class="mr-2 accent-[rgb(var(--color-primary))]" required>
                        I agree to <a href="#" class="underline text-[rgb(var(--color-primary))]">privacy policy & terms</a>
                    </label>

                    <button class="w-full btn btn-primary">Register</button>
                    <p class="text-xs text-gray-500 mt-4">Already have an account? <a href="{{ route('admin.login') }}" class="underline text-[rgb(var(--color-primary))]">Sign in instead</a></p>
                </form>
            </div>
        </div>
    </div>
    <div id="toastRoot" class="fixed z-[100] top-4 right-4 space-y-2 pointer-events-none"></div>
    @php
        $flash = [];
        if (session('success')) $flash[] = ['type' => 'success', 'text' => session('success')];
        if (session('error')) $flash[] = ['type' => 'error', 'text' => session('error')];
        if (session('warning')) $flash[] = ['type' => 'warning', 'text' => session('warning')];
        if (session('info')) $flash[] = ['type' => 'info', 'text' => session('info')];
        if ($errors && $errors->any()) {
            foreach ($errors->all() as $e) {
                $flash[] = ['type' => 'error', 'text' => $e];
            }
        }
    @endphp
    <script>
        window.__flash = {!! json_encode($flash) !!};
    </script>
</body>
</html>
