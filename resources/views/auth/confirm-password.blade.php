<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-slate-200">Confirm password</h2>
        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">This is a secure area. Please confirm your password before continuing.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Password</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
<svg class="h-5 w-5 text-gray-400 dark:text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password"
                    class="block w-full pl-10 pr-3 py-2.5 text-sm border border-gray-300 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 placeholder:text-gray-400 dark:placeholder:text-slate-500" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <button type="submit"
            class="w-full py-2.5 px-4 text-sm font-semibold text-white bg-gradient-to-r from-sky-600 to-cyan-600 rounded-lg hover:from-sky-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-slate-800 focus:ring-sky-500 transition shadow-sm">
            Confirm
        </button>
    </form>
</x-guest-layout>
