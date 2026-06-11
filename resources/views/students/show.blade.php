<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-white">{{ __('Student Profile') }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('students.edit', $student) }}"
                    class="bg-white/20 hover:bg-white/30 text-white rounded-xl px-4 py-2 text-sm font-medium backdrop-blur-sm inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('students.index') }}"
                    class="bg-white/10 hover:bg-white/20 text-white rounded-xl px-4 py-2 text-sm font-medium backdrop-blur-sm inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    {{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50/50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Personal Information') }}</h3>
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">{{ __('Name') }}</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $student->user->name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">{{ __('Email') }}</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $student->user->email }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">{{ __('Gender') }}</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $student->gender ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">{{ __('Date of Birth') }}</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : 'N/A' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">{{ __('Phone') }}</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $student->phone ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">{{ __('Address') }}</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $student->address ?? 'N/A' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-gray-50/50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Academic Information') }}</h3>
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">{{ __('Admission Number') }}</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $student->admission_number ?? 'N/A' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">{{ __('Classes') }}</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $student->classes->pluck('name')->join(', ') ?: 'Not Assigned' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">{{ __('Parent/Guardian') }}</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $student->parent?->user->name ?? 'Not Assigned' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">{{ __('Member Since') }}</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $student->created_at->format('M d, Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                @if ($student->classes->count() > 0)
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Assigned Classes') }}</h3>
                        <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 overflow-hidden border border-gray-100">
                            <table class="min-w-full divide-y divide-gray-100">
                                <thead class="bg-gray-50/50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Class Name') }}</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Assigned Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($student->classes as $class)
                                        <tr class="hover:bg-gray-50/50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $class->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $status = $class->pivot->status ?? 'active';
                                                    $badgeClass = match($status) {
                                                        'active' => 'bg-emerald-100 text-emerald-700',
                                                        'inactive' => 'bg-gray-100 text-gray-600',
                                                        'completed' => 'bg-blue-100 text-blue-700',
                                                        default => 'bg-gray-100 text-gray-600',
                                                    };
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                                    {{ ucfirst($status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $class->pivot->assigned_at ? $class->pivot->assigned_at->format('M d, Y') : '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
