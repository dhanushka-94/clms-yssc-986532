@php
    $attendeeTypes = [
        'players' => ['label' => 'Players', 'color' => 'blue'],
        'staff' => ['label' => 'Staff', 'color' => 'yellow'],
        'members' => ['label' => 'Members', 'color' => 'green']
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Event Attendance Report') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('events.attendances.export', ['event' => $event, 'format' => 'pdf']) }}" 
                   class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Export PDF
                </a>
                <a href="{{ route('events.attendances.export', ['event' => $event, 'format' => 'excel']) }}" 
                   class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Excel
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('events.attendances.report', $event) }}" method="GET" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Attendee Type Filter -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Attendee Type</label>
                                <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                                    <option value="">All Types</option>
                                    <option value="players" {{ request('type') === 'players' ? 'selected' : '' }}>Players</option>
                                    <option value="staff" {{ request('type') === 'staff' ? 'selected' : '' }}>Staff</option>
                                    <option value="members" {{ request('type') === 'members' ? 'selected' : '' }}>Members</option>
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                                    <option value="">All Status</option>
                                    <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Present</option>
                                    <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>Absent</option>
                                    <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Late</option>
                                    <option value="excused" {{ request('status') === 'excused' ? 'selected' : '' }}>Excused</option>
                                </select>
                            </div>

                            <!-- Search -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700">Search Name</label>
                                <input type="text" id="search" name="search" value="{{ request('search') }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500"
                                    placeholder="Search by name...">
                            </div>

                            <!-- Filter Button -->
                            <div class="flex items-end">
                                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                                    Apply Filters
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Event Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Event Details</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Title</p>
                            <p class="font-medium">{{ $event->title }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Type</p>
                            <p class="font-medium capitalize">{{ $event->type }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Date & Time</p>
                            <p class="font-medium">{{ $event->start_time->format('M d, Y h:i A') }} - {{ $event->end_time->format('h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Location</p>
                            <p class="font-medium">{{ $event->location }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Attendance Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($attendeeTypes as $type => $config)
                            <div class="bg-{{ $config['color'] }}-50 rounded-lg p-4">
                                <h4 class="text-{{ $config['color'] }}-700 font-medium">{{ $config['label'] }}</h4>
                                <div class="mt-2">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-sm font-medium text-gray-700">Attendance Rate</span>
                                        <span class="text-sm font-medium text-{{ $config['color'] }}-700">{{ $attendanceStats[$type]['rate'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-{{ $config['color'] }}-500 h-2 rounded-full" style="width: {{ $attendanceStats[$type]['rate'] }}%"></div>
                                    </div>
                                    <div class="mt-2 grid grid-cols-2 gap-2 text-sm">
                                        <div>
                                            <p class="text-gray-500">Present</p>
                                            <p class="font-medium text-gray-900">{{ $attendanceStats[$type]['present'] }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Total</p>
                                            <p class="font-medium text-gray-900">{{ $attendanceStats[$type]['total'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Detailed Attendance List -->
            @foreach($attendeeTypes as $type => $config)
                @if($attendanceStats[$type]['total'] > 0)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $config['label'] }} Attendance</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($attendanceStats[$type]['details'] as $attendance)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $attendance->attendee->first_name }} {{ $attendance->attendee->last_name }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $attendance->status === 'present' ? 'bg-green-100 text-green-800' : 
                                                           ($attendance->status === 'absent' ? 'bg-red-100 text-red-800' : 
                                                           'bg-yellow-100 text-yellow-800') }}">
                                                        {{ ucfirst($attendance->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $attendance->check_in_time ? $attendance->check_in_time->format('h:i A') : '-' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $attendance->check_out_time ? $attendance->check_out_time->format('h:i A') : '-' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $attendance->remarks ?? '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</x-app-layout> 