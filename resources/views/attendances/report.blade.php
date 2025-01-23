<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('Attendance Report') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $event->title }} - {{ $event->start_time->format('M d, Y h:i A') }} to {{ $event->end_time->format('h:i A') }}
                </p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('attendances.export.pdf', $event) }}" class="inline-flex items-center gap-x-1.5 px-3 py-2 text-sm font-semibold text-white bg-red-600 rounded-md shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                    <svg class="-ml-0.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 2.75a.75.75 0 00-1.5 0v8.614L6.295 8.235a.75.75 0 10-1.09 1.03l4.25 4.5a.75.75 0 001.09 0l4.25-4.5a.75.75 0 00-1.09-1.03l-2.955 3.129V2.75z" />
                        <path d="M3.5 12.75a.75.75 0 00-1.5 0v2.5A2.75 2.75 0 004.75 18h10.5A2.75 2.75 0 0018 15.25v-2.5a.75.75 0 00-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5z" />
                    </svg>
                    PDF
                </a>
                <a href="{{ route('attendances.export.excel', $event) }}" class="inline-flex items-center gap-x-1.5 px-3 py-2 text-sm font-semibold text-white bg-green-600 rounded-md shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
                    <svg class="-ml-0.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 2.75a.75.75 0 00-1.5 0v8.614L6.295 8.235a.75.75 0 10-1.09 1.03l4.25 4.5a.75.75 0 001.09 0l4.25-4.5a.75.75 0 00-1.09-1.03l-2.955 3.129V2.75z" />
                        <path d="M3.5 12.75a.75.75 0 00-1.5 0v2.5A2.75 2.75 0 004.75 18h10.5A2.75 2.75 0 0018 15.25v-2.5a.75.75 0 00-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5z" />
                    </svg>
                    Excel
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 gap-4 mb-8 sm:grid-cols-2 lg:grid-cols-5">
                <div class="p-4 bg-white rounded-lg shadow sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Players</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['total'] }}</dd>
                </div>
                <div class="p-4 bg-white rounded-lg shadow sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Present</dt>
                    <dd class="mt-1 text-3xl font-semibold text-green-600">{{ $stats['present'] }}</dd>
                    <dd class="mt-1 text-sm text-gray-500">{{ number_format(($stats['present'] / $stats['total']) * 100, 1) }}%</dd>
                </div>
                <div class="p-4 bg-white rounded-lg shadow sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Absent</dt>
                    <dd class="mt-1 text-3xl font-semibold text-red-600">{{ $stats['absent'] }}</dd>
                    <dd class="mt-1 text-sm text-gray-500">{{ number_format(($stats['absent'] / $stats['total']) * 100, 1) }}%</dd>
                </div>
                <div class="p-4 bg-white rounded-lg shadow sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Late</dt>
                    <dd class="mt-1 text-3xl font-semibold text-yellow-600">{{ $stats['late'] }}</dd>
                    <dd class="mt-1 text-sm text-gray-500">{{ number_format(($stats['late'] / $stats['total']) * 100, 1) }}%</dd>
                </div>
                <div class="p-4 bg-white rounded-lg shadow sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Excused</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-600">{{ $stats['excused'] }}</dd>
                    <dd class="mt-1 text-sm text-gray-500">{{ number_format(($stats['excused'] / $stats['total']) * 100, 1) }}%</dd>
                </div>
            </div>

            <!-- Detailed Report -->
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="sm:flex sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-medium leading-6 text-gray-900">Detailed Report</h3>
                            <p class="mt-1 text-sm text-gray-500">View detailed attendance records for each player</p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                            <div class="flex items-center space-x-2">
                                <button type="button" onclick="filterAttendance('all')" class="filter-btn px-3 py-2 text-sm font-medium rounded-md bg-gray-100 text-gray-700" data-status="all">All</button>
                                <button type="button" onclick="filterAttendance('present')" class="filter-btn px-3 py-2 text-sm font-medium rounded-md bg-green-100 text-green-700" data-status="present">Present</button>
                                <button type="button" onclick="filterAttendance('absent')" class="filter-btn px-3 py-2 text-sm font-medium rounded-md bg-red-100 text-red-700" data-status="absent">Absent</button>
                                <button type="button" onclick="filterAttendance('late')" class="filter-btn px-3 py-2 text-sm font-medium rounded-md bg-yellow-100 text-yellow-700" data-status="late">Late</button>
                                <button type="button" onclick="filterAttendance('excused')" class="filter-btn px-3 py-2 text-sm font-medium rounded-md bg-gray-100 text-gray-700" data-status="excused">Excused</button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flow-root">
                        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Player</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Check In</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Check Out</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Duration</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @forelse($attendances as $attendance)
                                            <tr class="attendance-row" data-status="{{ $attendance->status }}">
                                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                                                    {{ $attendance->player->name }}
                                                    @if($attendance->player->jersey_number)
                                                        <span class="ml-1 text-gray-500">#{{ $attendance->player->jersey_number }}</span>
                                                    @endif
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md
                                                        @if($attendance->status === 'present') bg-green-100 text-green-700
                                                        @elseif($attendance->status === 'absent') bg-red-100 text-red-700
                                                        @elseif($attendance->status === 'late') bg-yellow-100 text-yellow-700
                                                        @else bg-gray-100 text-gray-700
                                                        @endif">
                                                        {{ ucfirst($attendance->status) }}
                                                    </span>
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                    {{ $attendance->check_in_time?->format('h:i A') ?? '-' }}
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                    {{ $attendance->check_out_time?->format('h:i A') ?? '-' }}
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                    @if($attendance->check_in_time && $attendance->check_out_time)
                                                        {{ $attendance->check_in_time->diffInMinutes($attendance->check_out_time) }} minutes
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                    {{ $attendance->remarks ?? '-' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-3 py-4 text-sm text-center text-gray-500">
                                                    No attendance records found
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function filterAttendance(status) {
        const rows = document.querySelectorAll('.attendance-row');
        const buttons = document.querySelectorAll('.filter-btn');
        
        buttons.forEach(btn => {
            btn.classList.remove('ring-2', 'ring-offset-2');
            if (btn.dataset.status === status) {
                btn.classList.add('ring-2', 'ring-offset-2');
            }
        });

        rows.forEach(row => {
            if (status === 'all' || row.dataset.status === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Set initial filter to 'all' and highlight the button
    document.addEventListener('DOMContentLoaded', () => {
        const allButton = document.querySelector('.filter-btn[data-status="all"]');
        allButton.classList.add('ring-2', 'ring-offset-2');
    });
    </script>
</x-app-layout> 