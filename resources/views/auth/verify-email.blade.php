<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-slate-200">Verify email</h2>
        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Thanks for signing up! Verify your email to get started</p>
    </div>

    <div class="mb-6 text-sm text-gray-600 dark:text-slate-300 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 text-sm text-green-700 dark:text-green-200 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg p-4">
            A new verification link has been sent to the email address you provided during registration.
        </div>
    @endif

    <div class="flex flex-col gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                class="w-full py-2.5 px-4 text-sm font-semibold text-white bg-gradient-to-r from-sky-600 to-cyan-600 rounded-lg hover:from-sky-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-slate-800 focus:ring-sky-500 transition shadow-sm">
                Resend verification email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full py-2.5 px-4 text-sm font-medium text-gray-600 dark:text-slate-400 bg-gray-100 dark:bg-slate-700 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-slate-800 focus:ring-gray-400 transition">
                Log out
            </button>
        </form>
    </div>
</x-guest-layout>
