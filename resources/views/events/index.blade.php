<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Events') }}
            </h2>
            <a href="{{ route('events.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                {{ __('Create Event') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-900 text-xl font-semibold mb-2">{{ __('Upcoming Events') }}</div>
                    <div class="text-3xl font-bold text-indigo-600">{{ $upcomingEvents }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-900 text-xl font-semibold mb-2">{{ __('Completed Events') }}</div>
                    <div class="text-3xl font-bold text-green-600">{{ $completedEvents }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-900 text-xl font-semibold mb-2">{{ __('Total Events') }}</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $events->total() }}</div>
                </div>
            </div>

            <!-- Events List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Title') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Type') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Date & Time') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Location/Venue') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Status') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Attendance') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($events as $event)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $event->title }}
                                            </div>
                                            @if($event->type === 'match' && $event->opponent)
                                                <div class="text-sm text-gray-500">
                                                    vs {{ $event->opponent }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($event->type === 'match') bg-red-100 text-red-800
                                                @elseif($event->type === 'practice') bg-green-100 text-green-800
                                                @else bg-blue-100 text-blue-800 @endif">
                                                {{ ucfirst($event->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $event->start_time->format('M d, Y') }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $event->start_time->format('h:i A') }} - {{ $event->end_time->format('h:i A') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $event->location }}
                                                @if($event->type === 'match' && $event->venue)
                                                    <span class="text-xs text-gray-500">({{ ucfirst($event->venue) }})</span>
                                                @endif
                                            </div>
                                            @if($event->type === 'meeting' && $event->meeting_link)
                                                <a href="{{ $event->meeting_link }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-900">
                                                    {{ __('Join Meeting') }}
                                                </a>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($event->status === 'scheduled') bg-yellow-100 text-yellow-800
                                                @elseif($event->status === 'in_progress') bg-blue-100 text-blue-800
                                                @elseif($event->status === 'completed') bg-green-100 text-green-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ str_replace('_', ' ', ucfirst($event->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $event->players->count() }} {{ __('Players') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-3">
                                                <a href="{{ route('events.show', $event) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ __('View') }}
                                                </a>
                                                <a href="{{ route('events.edit', $event) }}" class="text-yellow-600 hover:text-yellow-900">
                                                    {{ __('Edit') }}
                                                </a>
                                                @if($event->status === 'scheduled')
                                                    <a href="{{ route('events.attendances.create', $event) }}" class="text-green-600 hover:text-green-900">
                                                        {{ __('Take Attendance') }}
                                                    </a>
                                                @endif
                                                <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this event?')">
                                                        {{ __('Delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ __('No events found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $events->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 