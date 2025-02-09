@php
    $eventTypes = [
        'match' => ['icon' => 'trophy', 'color' => 'blue'],
        'practice' => ['icon' => 'users', 'color' => 'yellow'],
        'meeting' => ['icon' => 'presentation-chart-bar', 'color' => 'green']
    ];

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
                {{ __('Attendance Report') }} <span class="text-sm text-gray-500">(Last 30 Days)</span>
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Attendee Type Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Attendance by Type</h3>
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

            <!-- Event Type Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Events Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($eventTypes as $type => $config)
                            <div class="bg-{{ $config['color'] }}-50 rounded-lg p-4">
                                <h4 class="text-{{ $config['color'] }}-700 font-medium capitalize">{{ $type }}s</h4>
                                <div class="mt-2">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-sm font-medium text-gray-700">Attendance Rate</span>
                                        <span class="text-sm font-medium text-{{ $config['color'] }}-700">{{ $eventStats[$type]['attendance_rate'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-{{ $config['color'] }}-500 h-2 rounded-full" style="width: {{ $eventStats[$type]['attendance_rate'] }}%"></div>
                                    </div>
                                    <div class="mt-2 grid grid-cols-2 gap-2 text-sm">
                                        <div>
                                            <p class="text-gray-500">Total Events</p>
                                            <p class="font-medium text-gray-900">{{ $eventStats[$type]['total_events'] }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Present</p>
                                            <p class="font-medium text-gray-900">{{ $eventStats[$type]['present_count'] }}/{{ $eventStats[$type]['total_attendances'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Recent Events -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Events</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($recentEvents as $event)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $event->title }}</div>
                                            <div class="text-sm text-gray-500">{{ $event->location }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $event->start_time->format('M d, Y') }}</div>
                                            <div class="text-sm text-gray-500">{{ $event->start_time->format('h:i A') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $eventTypes[$event->type]['color'] }}-100 text-{{ $eventTypes[$event->type]['color'] }}-800">
                                                {{ ucfirst($event->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="text-sm text-gray-900">
                                                    {{ $event->attendances->where('status', 'present')->count() }}/{{ $event->attendances->count() }}
                                                </div>
                                                <div class="ml-2 flex-shrink-0">
                                                    <div class="h-2 w-24 bg-gray-200 rounded-full overflow-hidden">
                                                        @php
                                                            $percentage = $event->attendances->count() > 0 
                                                                ? ($event->attendances->where('status', 'present')->count() / $event->attendances->count()) * 100 
                                                                : 0;
                                                        @endphp
                                                        <div class="h-full bg-green-500" style="width: {{ $percentage }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No recent events found.
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
</x-app-layout> 