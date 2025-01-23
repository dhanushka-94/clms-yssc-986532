<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Attendance Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 gap-4 mb-8 sm:grid-cols-2 lg:grid-cols-6">
                <!-- Match Stats -->
                <div class="p-4 bg-purple-50 rounded-lg shadow sm:p-6">
                    <dt class="text-sm font-medium text-purple-700 truncate">Total Matches</dt>
                    <dd class="mt-1 text-3xl font-semibold text-purple-900">{{ $stats['match']['total'] }}</dd>
                    <p class="mt-2 text-sm text-purple-600">
                        Present Rate: {{ $stats['match']['total'] > 0 ? number_format(($stats['match']['present_rate'] / ($stats['match']['present_rate'] + $stats['match']['absent_rate'])) * 100, 1) : 0 }}%
                    </p>
                </div>

                <!-- Practice Stats -->
                <div class="p-4 bg-blue-50 rounded-lg shadow sm:p-6">
                    <dt class="text-sm font-medium text-blue-700 truncate">Total Practices</dt>
                    <dd class="mt-1 text-3xl font-semibold text-blue-900">{{ $stats['practice']['total'] }}</dd>
                    <p class="mt-2 text-sm text-blue-600">
                        Present Rate: {{ $stats['practice']['total'] > 0 ? number_format(($stats['practice']['present_rate'] / ($stats['practice']['present_rate'] + $stats['practice']['absent_rate'])) * 100, 1) : 0 }}%
                    </p>
                </div>

                <!-- Meeting Stats -->
                <div class="p-4 bg-gray-50 rounded-lg shadow sm:p-6">
                    <dt class="text-sm font-medium text-gray-700 truncate">Total Meetings</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['meeting']['total'] }}</dd>
                    <p class="mt-2 text-sm text-gray-600">
                        Present Rate: {{ $stats['meeting']['total'] > 0 ? number_format(($stats['meeting']['present_rate'] / ($stats['meeting']['present_rate'] + $stats['meeting']['absent_rate'])) * 100, 1) : 0 }}%
                    </p>
                </div>

                <!-- People Stats -->
                <div class="p-4 bg-white rounded-lg shadow sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Active Players</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['total_players'] }}</dd>
                </div>
                <div class="p-4 bg-white rounded-lg shadow sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Active Staff</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['total_staff'] }}</dd>
                </div>
                <div class="p-4 bg-white rounded-lg shadow sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">Active Members</dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $stats['total_members'] }}</dd>
                </div>
            </div>

            <!-- Events List -->
            @foreach(['match' => 'Matches', 'practice' => 'Practice Sessions', 'meeting' => 'Meetings'] as $type => $title)
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg mb-8">
                    <div class="p-6 text-gray-900">
                        <div class="sm:flex sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-lg font-medium leading-6 text-gray-900">{{ $title }}</h3>
                                <p class="mt-1 text-sm text-gray-500">Manage attendance for {{ strtolower($title) }}</p>
                            </div>
                        </div>

                        <div class="mt-8 flow-root">
                            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                    <table class="min-w-full divide-y divide-gray-300">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Event</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date & Time</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Players</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Staff</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Members</th>
                                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                                    <span class="sr-only">Actions</span>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @forelse($eventsByType[$type] as $event)
                                                <tr>
                                                    <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                                                        {{ $event->title }}
                                                    </td>
                                                    <td class="px-3 py-4 text-sm text-gray-500">
                                                        {{ $event->start_time->format('M d, Y h:i A') }}
                                                    </td>
                                                    <td class="px-3 py-4 text-sm text-gray-500">
                                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md
                                                            @if($event->status === 'scheduled') bg-yellow-100 text-yellow-700
                                                            @elseif($event->status === 'in_progress') bg-blue-100 text-blue-700
                                                            @elseif($event->status === 'completed') bg-green-100 text-green-700
                                                            @else bg-red-100 text-red-700
                                                            @endif">
                                                            {{ str_replace('_', ' ', ucfirst($event->status)) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-3 py-4 text-sm text-gray-500">
                                                        @php
                                                            $playerStats = $event->getAttendanceStatsByType('players');
                                                        @endphp
                                                        @if($playerStats['total'] > 0)
                                                            <div class="flex items-center gap-2">
                                                                <div class="w-16 h-2 bg-gray-200 rounded-full">
                                                                    <div class="h-2 bg-green-500 rounded-full" style="width: {{ ($playerStats['present'] / $playerStats['total']) * 100 }}%"></div>
                                                                </div>
                                                                <span>{{ $playerStats['present'] }}/{{ $playerStats['total'] }}</span>
                                                            </div>
                                                        @else
                                                            <span class="text-gray-400">No records</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-4 text-sm text-gray-500">
                                                        @php
                                                            $staffStats = $event->getAttendanceStatsByType('staff');
                                                        @endphp
                                                        @if($staffStats['total'] > 0)
                                                            <div class="flex items-center gap-2">
                                                                <div class="w-16 h-2 bg-gray-200 rounded-full">
                                                                    <div class="h-2 bg-green-500 rounded-full" style="width: {{ ($staffStats['present'] / $staffStats['total']) * 100 }}%"></div>
                                                                </div>
                                                                <span>{{ $staffStats['present'] }}/{{ $staffStats['total'] }}</span>
                                                            </div>
                                                        @else
                                                            <span class="text-gray-400">No records</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-3 py-4 text-sm text-gray-500">
                                                        @php
                                                            $memberStats = $event->getAttendanceStatsByType('members');
                                                        @endphp
                                                        @if($memberStats['total'] > 0)
                                                            <div class="flex items-center gap-2">
                                                                <div class="w-16 h-2 bg-gray-200 rounded-full">
                                                                    <div class="h-2 bg-green-500 rounded-full" style="width: {{ ($memberStats['present'] / $memberStats['total']) * 100 }}%"></div>
                                                                </div>
                                                                <span>{{ $memberStats['present'] }}/{{ $memberStats['total'] }}</span>
                                                            </div>
                                                        @else
                                                            <span class="text-gray-400">No records</span>
                                                        @endif
                                                    </td>
                                                    <td class="relative py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                                        @if($event->status === 'scheduled' || $event->status === 'in_progress')
                                                            <div class="flex items-center justify-end gap-2">
                                                                <a href="{{ route('events.attendances.create', $event) }}?type=players" class="text-indigo-600 hover:text-indigo-900">Players</a>
                                                                <span class="text-gray-300">|</span>
                                                                <a href="{{ route('events.attendances.create', $event) }}?type=staff" class="text-indigo-600 hover:text-indigo-900">Staff</a>
                                                                <span class="text-gray-300">|</span>
                                                                <a href="{{ route('events.attendances.create', $event) }}?type=members" class="text-indigo-600 hover:text-indigo-900">Members</a>
                                                            </div>
                                                        @else
                                                            <div class="flex items-center justify-end gap-2">
                                                                <a href="{{ route('events.attendances.report', $event) }}?type=players" class="text-gray-600 hover:text-gray-900">Players</a>
                                                                <span class="text-gray-300">|</span>
                                                                <a href="{{ route('events.attendances.report', $event) }}?type=staff" class="text-gray-600 hover:text-gray-900">Staff</a>
                                                                <span class="text-gray-300">|</span>
                                                                <a href="{{ route('events.attendances.report', $event) }}?type=members" class="text-gray-600 hover:text-gray-900">Members</a>
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="px-3 py-4 text-sm text-center text-gray-500">
                                                        No {{ strtolower($title) }} found
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
            @endforeach
        </div>
    </div>
</x-app-layout> 