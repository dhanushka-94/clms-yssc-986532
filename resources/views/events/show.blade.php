<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $event->title }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('events.edit', $event) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700">
                    {{ __('Edit Event') }}
                </a>
                @if($event->status === 'scheduled')
                    <a href="{{ route('events.attendances.create', $event) }}?type=players" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                        {{ __('Take Attendance') }}
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Event Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Event Details') }}</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Type') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($event->type === 'match') bg-red-100 text-red-800
                                            @elseif($event->type === 'practice') bg-green-100 text-green-800
                                            @else bg-blue-100 text-blue-800 @endif">
                                            {{ ucfirst($event->type) }}
                                        </span>
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Date & Time') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $event->start_time->format('M d, Y') }}<br>
                                        {{ $event->start_time->format('h:i A') }} - {{ $event->end_time->format('h:i A') }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Location') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $event->location }}
                                        @if($event->type === 'match' && $event->venue)
                                            <span class="text-xs text-gray-500">({{ ucfirst($event->venue) }})</span>
                                        @endif
                                    </dd>
                                </div>

                                @if($event->type === 'match')
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Opponent') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $event->opponent }}</dd>
                                    </div>
                                @endif

                                @if($event->type === 'meeting' && $event->meeting_link)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Meeting Link') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            <a href="{{ $event->meeting_link }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                                {{ __('Join Meeting') }}
                                            </a>
                                        </dd>
                                    </div>
                                @endif

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($event->status === 'scheduled') bg-yellow-100 text-yellow-800
                                            @elseif($event->status === 'in_progress') bg-blue-100 text-blue-800
                                            @elseif($event->status === 'completed') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ str_replace('_', ' ', ucfirst($event->status)) }}
                                        </span>
                                    </dd>
                                </div>

                                @if($event->description)
                                    <div class="col-span-full">
                                        <dt class="text-sm font-medium text-gray-500">{{ __('Description') }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $event->description }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Attendance Summary') }}</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-green-50 p-4 rounded-lg">
                                    <dt class="text-sm font-medium text-green-800">{{ __('Present') }}</dt>
                                    <dd class="mt-1 text-2xl font-semibold text-green-600">{{ $attendanceStats['present'] }}</dd>
                                </div>
                                <div class="bg-red-50 p-4 rounded-lg">
                                    <dt class="text-sm font-medium text-red-800">{{ __('Absent') }}</dt>
                                    <dd class="mt-1 text-2xl font-semibold text-red-600">{{ $attendanceStats['absent'] }}</dd>
                                </div>
                                <div class="bg-yellow-50 p-4 rounded-lg">
                                    <dt class="text-sm font-medium text-yellow-800">{{ __('Late') }}</dt>
                                    <dd class="mt-1 text-2xl font-semibold text-yellow-600">{{ $attendanceStats['late'] }}</dd>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <dt class="text-sm font-medium text-gray-800">{{ __('Excused') }}</dt>
                                    <dd class="mt-1 text-2xl font-semibold text-gray-600">{{ $attendanceStats['excused'] }}</dd>
                                </div>
                            </div>

                            @if($event->attachments)
                                <div class="mt-6">
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">{{ __('Attachments') }}</h4>
                                    <ul class="divide-y divide-gray-200">
                                        @foreach($event->attachments as $attachment)
                                            <li class="py-2">
                                                <a href="{{ Storage::url($attachment) }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-900">
                                                    {{ basename($attachment) }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Attendance List') }}</h3>
                        @if($event->status === 'scheduled')
                            <a href="{{ route('events.attendances.edit', $event) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                {{ __('Update Attendance') }}
                            </a>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Player') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Status') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Check In') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Check Out') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Remarks') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($event->attendances as $attendance)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $attendance->player->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $attendance->player->jersey_number }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($attendance->status === 'present') bg-green-100 text-green-800
                                                @elseif($attendance->status === 'absent') bg-red-100 text-red-800
                                                @elseif($attendance->status === 'late') bg-yellow-100 text-yellow-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($attendance->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $attendance->check_in_time ? $attendance->check_in_time->format('h:i A') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $attendance->check_out_time ? $attendance->check_out_time->format('h:i A') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $attendance->remarks ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ __('No attendance records found.') }}
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