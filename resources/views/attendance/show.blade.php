<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-white">{{ __('Attendance Sheet') }}</h2>
            <div class="flex items-center gap-2">
                @if(Auth::user()->hasRole('Teacher'))
                    <a href="{{ route('attendances.edit', $attendance) }}" title="Edit"
                        class="inline-flex items-center justify-center w-9 h-9 text-white/80 hover:text-white hover:bg-white/20 rounded-xl transition">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                @endif
                <a href="{{ route('attendances.index') }}" title="Back to list"
                    class="inline-flex items-center justify-center w-9 h-9 text-white/80 hover:text-white hover:bg-white/20 rounded-xl transition">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-gray-50/50 rounded-xl p-6">
                    <p class="text-sm text-gray-500">Class</p>
                    <p class="text-lg font-semibold text-gray-900 mt-1">{{ $attendance->schoolClass->name }}</p>
                </div>
                <div class="bg-gray-50/50 rounded-xl p-6">
                    <p class="text-sm text-gray-500">Date</p>
                    <p class="text-lg font-semibold text-gray-900 mt-1">{{ $attendance->attendance_date->format('l, F d, Y') }}</p>
                </div>
                <div class="bg-gray-50/50 rounded-xl p-6">
                    <p class="text-sm text-gray-500">Recorded By</p>
                    <p class="text-lg font-semibold text-gray-900 mt-1">{{ $attendance->creator->name }}</p>
                </div>
            </div>

            @if($attendance->notes)
                <div class="bg-gray-50/50 rounded-xl p-6 mb-6">
                    <p class="text-sm text-gray-500">Notes</p>
                    <p class="text-gray-900 mt-1">{{ $attendance->notes }}</p>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-lg shadow-gray-200/50 overflow-hidden p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Admission</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($attendance->records as $record)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $record->student->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->student->admission_number ?? '—' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'present' => 'bg-green-100 text-green-700',
                                                'absent' => 'bg-red-100 text-red-700',
                                                'late' => 'bg-yellow-100 text-yellow-700',
                                                'excused' => 'bg-blue-100 text-blue-700',
                                            ];
                                            $color = $statusColors[$record->status] ?? 'bg-gray-100 text-gray-700';
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $color }}">
                                            {{ ucfirst($record->status) }}
                                        </span>
                                    </td>
                                    @if(Auth::user()->hasRole('Teacher'))
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <form action="{{ route('attendance-records.update', $record) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <select name="status" onchange="this.form.submit()"
                                                class="rounded-xl border-gray-200 text-xs focus:ring-2 focus:ring-sky-500 focus:border-sky-500 py-1.5 px-3">
                                                <option value="present" {{ $record->status === 'present' ? 'selected' : '' }}>Present</option>
                                                <option value="absent" {{ $record->status === 'absent' ? 'selected' : '' }}>Absent</option>
                                                <option value="late" {{ $record->status === 'late' ? 'selected' : '' }}>Late</option>
                                                <option value="excused" {{ $record->status === 'excused' ? 'selected' : '' }}>Excused</option>
                                            </select>
                                        </form>
                                    </td>
                                    @else
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm"></td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">No records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
