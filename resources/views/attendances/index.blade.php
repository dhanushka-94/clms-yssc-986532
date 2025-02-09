@php
    $eventTypes = [
        'match' => ['icon' => 'trophy', 'color' => 'blue'],
        'practice' => ['icon' => 'users', 'color' => 'yellow'],
        'meeting' => ['icon' => 'presentation-chart-bar', 'color' => 'green']
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Attendance Management') }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('attendances.report') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    View Report
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Active Members Stats -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Members</p>
                            <div class="mt-2 flex items-baseline">
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_players'] + $stats['total_staff'] + $stats['total_members'] }}</p>
                                <p class="ml-2 text-sm text-gray-600">total</p>
                            </div>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-2 text-sm">
                        <div class="text-center">
                            <p class="font-medium text-gray-900">{{ $stats['total_players'] }}</p>
                            <p class="text-gray-600">Players</p>
                        </div>
                        <div class="text-center">
                            <p class="font-medium text-gray-900">{{ $stats['total_staff'] }}</p>
                            <p class="text-gray-600">Staff</p>
                        </div>
                        <div class="text-center">
                            <p class="font-medium text-gray-900">{{ $stats['total_members'] }}</p>
                            <p class="text-gray-600">Members</p>
                        </div>
                    </div>
                </div>

                <!-- Attendance Overview -->
                <div class="col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-600">Attendance Overview</p>
                    <div class="mt-4 grid grid-cols-3 gap-4">
                        @foreach(['match', 'practice', 'meeting'] as $type)
                            <div class="bg-{{ $eventTypes[$type]['color'] }}-50 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-{{ $eventTypes[$type]['color'] }}-700 capitalize">{{ $type }}s</p>
                                    <span class="bg-{{ $eventTypes[$type]['color'] }}-100 text-{{ $eventTypes[$type]['color'] }}-700 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        {{ $stats[$type]['total'] }}
                                    </span>
                                </div>
                                <div class="mt-2 grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <p class="text-green-600 font-medium">{{ $stats[$type]['present_rate'] }}</p>
                                        <p class="text-gray-600 text-xs">Present</p>
                                    </div>
                                    <div>
                                        <p class="text-red-600 font-medium">{{ $stats[$type]['absent_rate'] }}</p>
                                        <p class="text-gray-600 text-xs">Absent</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Event Type Tabs -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex" aria-label="Tabs">
                        @foreach(['match', 'practice', 'meeting'] as $type)
                            <button onclick="switchTab('{{ $type }}')" 
                                    class="tab-button w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm
                                           {{ $loop->first ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                    data-tab="{{ $type }}">
                                <span class="capitalize">{{ $type }}s</span>
                                <span class="ml-2 bg-{{ $eventTypes[$type]['color'] }}-100 text-{{ $eventTypes[$type]['color'] }}-700 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    {{ $eventsByType[$type]->count() }}
                                </span>
                            </button>
                        @endforeach
                    </nav>
                </div>

                <!-- Event Lists -->
                @foreach(['match', 'practice', 'meeting'] as $type)
                    <div id="{{ $type }}-tab" class="tab-content p-6 {{ !$loop->first ? 'hidden' : '' }}">
                        @if($eventsByType[$type]->isEmpty())
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No {{ $type }}s found</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating a new {{ $type }}.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($eventsByType[$type] as $event)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $event->title }}</div>
                                                    <div class="text-sm text-gray-500">{{ $event->location }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $event->start_time->format('M d, Y') }}</div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $event->start_time->format('h:i A') }} - {{ $event->end_time->format('h:i A') }}
                                                    </div>
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
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $event->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                           ($event->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                                           'bg-yellow-100 text-yellow-800') }}">
                                                        {{ ucfirst($event->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div class="flex justify-end space-x-3">
                                                        <a href="{{ route('events.attendances.create', ['event' => $event]) }}" 
                                                           class="text-yellow-600 hover:text-yellow-900">
                                                            Record
                                                        </a>
                                                        <a href="{{ route('events.attendances.report', ['event' => $event]) }}" 
                                                           class="text-blue-600 hover:text-blue-900 flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                            </svg>
                                                            Report
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function switchTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Show selected tab content
            document.getElementById(tabId + '-tab').classList.remove('hidden');

            // Update tab button styles
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('border-yellow-500', 'text-yellow-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });

            // Add active styles to clicked tab
            document.querySelector(`[data-tab="${tabId}"]`).classList.remove('border-transparent', 'text-gray-500');
            document.querySelector(`[data-tab="${tabId}"]`).classList.add('border-yellow-500', 'text-yellow-600');
        }
    </script>
    @endpush
</x-app-layout> 