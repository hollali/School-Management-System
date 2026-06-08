<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-50 text-slate-900">
    <div class="mx-auto flex min-h-screen max-w-5xl flex-col justify-center px-6 py-12">
        <div class="rounded-3xl border border-slate-200 bg-white p-10 shadow-xl">
            <div class="mb-8 text-center">
                <p class="text-sm uppercase tracking-[0.3em] text-sky-600">Welcome</p>
                <h1 class="mt-4 text-4xl font-semibold sm:text-5xl">Your home page is ready</h1>
                <p class="mt-4 text-base leading-7 text-slate-600">A simple authenticated landing page for your
                    application.</p>
            </div>

            @if (isset($user))
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                    <p class="text-sm text-slate-500">Logged in as</p>
                    <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $user }}</p>
                </div>
                <form action="/logout" method="POST" class="mt-8">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-2xl bg-sky-600 px-6 py-3 text-sm font-semibold text-white shadow-md shadow-sky-500/20 transition hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 focus:ring-offset-slate-50">
                        Log out
                    </button>
                </form>
            @else
                <div class="grid gap-4 sm:grid-cols-2">
                    <a href="/login"
                        class="rounded-2xl border border-slate-200 bg-slate-50 px-6 py-4 text-center text-lg font-semibold text-slate-900 transition hover:bg-slate-100">
                        Login
                    </a>
                    <a href="/register"
                        class="rounded-2xl bg-sky-600 px-6 py-4 text-center text-lg font-semibold text-white shadow-md shadow-sky-500/20 transition hover:bg-sky-700">
                        Register
                    </a>
                </div>
            @endif
        </div>
    </div>
</body>

</html>
