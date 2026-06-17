@section('title', 'Profile')

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">{{ __('Profile') }}</h2>
        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">Manage your account settings</p>
    </x-slot>

    <div class="py-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 text-center">
                <div class="mb-4">
                    <img src="{{ $user->profile_photo_url }}" alt="" class="w-24 h-24 rounded-full mx-auto shadow-lg">
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-slate-200">{{ $user->name }}</h3>
                <p class="text-sm text-gray-400 dark:text-slate-500">{{ $user->email }}</p>
                <span class="inline-flex items-center px-3 py-1 mt-2 rounded-full text-xs font-medium
                    @if($user->role === 'Admin') bg-purple-100 text-purple-700
                    @elseif($user->role === 'Teacher') bg-sky-100 text-sky-700
                    @elseif($user->role === 'Student') bg-emerald-100 text-emerald-700
                    @else bg-amber-100 text-amber-700
                    @endif">
                    {{ $user->role }}
                </span>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
</x-app-layout>
