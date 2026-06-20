<x-app-layout>
    @section('title', 'Edit Holiday')

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-slate-200">Edit Holiday</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">{{ $holiday->name }}</p>
            </div>
            <a href="{{ route('holidays.index') }}"
               class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6">
                <form action="{{ route('holidays.update', $holiday) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Holiday Name</label>
                            <input type="text" name="name" value="{{ old('name', $holiday->name) }}" required
                                   class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                            @error('name')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Date</label>
                            <input type="date" name="holiday_date" value="{{ old('holiday_date', $holiday->holiday_date->format('Y-m-d')) }}" required
                                   class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                            @error('holiday_date')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Type</label>
                            <select name="type" required
                                    class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">
                                <option value="public" {{ old('type', $holiday->type) === 'public' ? 'selected' : '' }}>Public Holiday</option>
                                <option value="school" {{ old('type', $holiday->type) === 'school' ? 'selected' : '' }}>School Holiday</option>
                                <option value="exam" {{ old('type', $holiday->type) === 'exam' ? 'selected' : '' }}>Exam Day</option>
                            </select>
                            @error('type')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="recurring" value="1" {{ old('recurring', $holiday->recurring) ? 'checked' : '' }}
                                       class="rounded border-gray-300 dark:border-slate-600 text-sky-600 focus:ring-sky-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-slate-300">Recurs every year</span>
                            </label>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Description (optional)</label>
                            <textarea name="description" rows="3"
                                      class="block w-full rounded-xl border-gray-200 dark:border-slate-600 dark:text-slate-200 dark:bg-slate-700 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm py-2.5 px-4 bg-white">{{ old('description', $holiday->description) }}</textarea>
                            @error('description')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <a href="{{ route('holidays.index') }}"
                           class="px-4 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 text-sm font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-600 transition">
                            Cancel
                        </a>
                        <button type="submit"
                                class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-xl transition">
                            <i class="fa-solid fa-check mr-1.5"></i>
                            Update Holiday
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
