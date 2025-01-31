<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('Take Attendance') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $event->title }} - {{ $event->start_time->format('M d, Y h:i A') }} to {{ $event->end_time->format('h:i A') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('events.attendances.store', $event) }}" class="p-6">
                    @csrf

                    <x-attendances.form :event="$event" :attendees="$attendees" :existingAttendances="$existingAttendances" :type="$type" />

                    <div class="flex items-center justify-end mt-6 gap-x-6">
                        <a href="{{ route('events.show', $event) }}" class="text-sm font-semibold leading-6 text-gray-900">Cancel</a>
                        <button type="submit" class="px-3 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-md shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            Save Attendance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout> 