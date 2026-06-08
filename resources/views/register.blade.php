<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-50 flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-2xl bg-white rounded-3xl shadow-xl ring-1 ring-slate-200 overflow-hidden">
        <div class="px-8 py-10 sm:px-12">
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-semibold text-slate-900">Create your account</h1>
                <p class="mt-3 text-sm text-slate-500">Join now and start managing your profile.</p>
            </div>

            @if(session('status'))
                <div class="mb-6 rounded-2xl bg-emerald-50 px-5 py-4 text-sm text-emerald-700 ring-1 ring-emerald-200">
                    {{ session('status') }}
                </div>
            @endif

            <form action="/register" method="POST" autocomplete="off" class="space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" autocomplete="off" required
                        class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200" />
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Email address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="off" required
                        class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200" />
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                        <input type="password" name="password" id="password" autocomplete="new-password" required
                            class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200" />
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm
                            password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            autocomplete="new-password" required
                            class="mt-2 block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200" />
                    </div>
                </div>

                <div class="flex items-center justify-between gap-4 text-sm text-slate-500">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="terms" required
                            class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500" />
                        <span>I agree to the terms and privacy policy</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full rounded-2xl bg-sky-600 px-5 py-3 text-sm font-semibold text-white shadow-md shadow-sky-500/20 transition hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 focus:ring-offset-slate-50">
                    Register
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-500">
                Already have an account? <a href="/login" class="font-semibold text-sky-600 hover:text-sky-700">Login
                    here</a>
            </p>
        </div>
    </div>
</body>

</html>
