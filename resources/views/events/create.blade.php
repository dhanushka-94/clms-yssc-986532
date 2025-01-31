<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Event') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('events.store') }}" enctype="multipart/form-data">
                        @csrf
                        <x-events.form 
                            :players="$players" 
                            :staff="$staff" 
                            :members="$members"
                            :selectedPlayers="old('players', [])"
                            :selectedStaff="old('staff', [])"
                            :selectedMembers="old('members', [])"
                        />
                        <div class="mt-6 flex justify-end">
                            <x-secondary-button type="button" onclick="window.history.back()">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            <x-primary-button class="ml-3">
                                {{ __('Create Event') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 