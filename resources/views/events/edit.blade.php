@php
    $title = $event->title;
@endphp

<x-app-layout>
    <x-slot:header>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Event') }}: {{ $title }}
        </h2>
    </x-slot:header>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold text-gray-900">{{ __('Edit Event') }}</h2>
                        <a href="{{ route('events.show', $event) }}" class="text-yellow-600 hover:text-yellow-900">{{ __('Back to Event') }}</a>
                    </div>

                    <form method="POST" action="{{ route('events.update', $event) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <x-events.form 
                            :event="$event" 
                            :players="$players" 
                            :staff="$staff"
                            :members="$members"
                            :selectedPlayers="$selectedPlayers"
                            :selectedStaff="$selectedStaff"
                            :selectedMembers="$selectedMembers" />

                        <div class="mt-6 flex items-center justify-end gap-x-6">
                            <a href="{{ route('events.show', $event) }}" class="text-sm font-semibold leading-6 text-gray-900">{{ __('Cancel') }}</a>
                            <button type="submit" class="rounded-md bg-yellow-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-yellow-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-yellow-600">{{ __('Update Event') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 