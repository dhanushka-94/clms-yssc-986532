@props(['event' => null, 'players' => [], 'selectedPlayers' => []])

<div class="space-y-6">
    <!-- Title -->
    <div>
        <x-input-label for="title" :value="__('Title')" />
        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $event?->title)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('title')" />
    </div>

    <!-- Description -->
    <div>
        <x-input-label for="description" :value="__('Description')" />
        <x-textarea-input id="description" name="description" class="mt-1 block w-full" rows="3">{{ old('description', $event?->description) }}</x-textarea-input>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    <!-- Type -->
    <div>
        <x-input-label for="type" :value="__('Event Type')" />
        <select id="type" name="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
            <option value="">{{ __('Select Type') }}</option>
            <option value="match" {{ old('type', $event?->type) === 'match' ? 'selected' : '' }}>{{ __('Match') }}</option>
            <option value="practice" {{ old('type', $event?->type) === 'practice' ? 'selected' : '' }}>{{ __('Practice') }}</option>
            <option value="meeting" {{ old('type', $event?->type) === 'meeting' ? 'selected' : '' }}>{{ __('Meeting') }}</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('type')" />
    </div>

    <!-- Date and Time -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <x-input-label for="start_time" :value="__('Start Time')" />
            <x-text-input id="start_time" name="start_time" type="datetime-local" class="mt-1 block w-full" :value="old('start_time', $event?->start_time?->format('Y-m-d\TH:i'))" required />
            <x-input-error class="mt-2" :messages="$errors->get('start_time')" />
        </div>

        <div>
            <x-input-label for="end_time" :value="__('End Time')" />
            <x-text-input id="end_time" name="end_time" type="datetime-local" class="mt-1 block w-full" :value="old('end_time', $event?->end_time?->format('Y-m-d\TH:i'))" required />
            <x-input-error class="mt-2" :messages="$errors->get('end_time')" />
        </div>
    </div>

    <!-- Location -->
    <div>
        <x-input-label for="location" :value="__('Location')" />
        <x-text-input id="location" name="location" type="text" class="mt-1 block w-full" :value="old('location', $event?->location)" />
        <x-input-error class="mt-2" :messages="$errors->get('location')" />
    </div>

    <!-- Match Specific Fields -->
    <div id="match-fields" class="space-y-6" style="display: none;">
        <div>
            <x-input-label for="opponent" :value="__('Opponent')" />
            <x-text-input id="opponent" name="opponent" type="text" class="mt-1 block w-full" :value="old('opponent', $event?->opponent)" />
            <x-input-error class="mt-2" :messages="$errors->get('opponent')" />
        </div>

        <div>
            <x-input-label for="venue" :value="__('Venue')" />
            <select id="venue" name="venue" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="">{{ __('Select Venue') }}</option>
                <option value="home" {{ old('venue', $event?->venue) === 'home' ? 'selected' : '' }}>{{ __('Home') }}</option>
                <option value="away" {{ old('venue', $event?->venue) === 'away' ? 'selected' : '' }}>{{ __('Away') }}</option>
                <option value="neutral" {{ old('venue', $event?->venue) === 'neutral' ? 'selected' : '' }}>{{ __('Neutral') }}</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('venue')" />
        </div>
    </div>

    <!-- Meeting Specific Fields -->
    <div id="meeting-fields" class="space-y-6" style="display: none;">
        <div>
            <x-input-label for="meeting_link" :value="__('Meeting Link')" />
            <x-text-input id="meeting_link" name="meeting_link" type="url" class="mt-1 block w-full" :value="old('meeting_link', $event?->meeting_link)" />
            <x-input-error class="mt-2" :messages="$errors->get('meeting_link')" />
        </div>
    </div>

    <!-- Status -->
    <div>
        <x-input-label for="status" :value="__('Status')" />
        <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
            <option value="scheduled" {{ old('status', $event?->status) === 'scheduled' ? 'selected' : '' }}>{{ __('Scheduled') }}</option>
            <option value="in_progress" {{ old('status', $event?->status) === 'in_progress' ? 'selected' : '' }}>{{ __('In Progress') }}</option>
            <option value="completed" {{ old('status', $event?->status) === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
            <option value="cancelled" {{ old('status', $event?->status) === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('status')" />
    </div>

    <!-- Players -->
    <div>
        <x-input-label for="players" :value="__('Select Players')" />
        <select id="players" name="players[]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" multiple>
            @foreach($players as $player)
                <option value="{{ $player->id }}" {{ in_array($player->id, old('players', $selectedPlayers)) ? 'selected' : '' }}>
                    {{ $player->name }}
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('players')" />
    </div>

    <!-- Attachments -->
    <div>
        <x-input-label for="attachments" :value="__('Attachments')" />
        <input id="attachments" name="attachments[]" type="file" class="mt-1 block w-full" multiple>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Allowed file types: Images, PDF, DOC, DOCX. Maximum size: 2MB per file.') }}
        </p>
        <x-input-error class="mt-2" :messages="$errors->get('attachments.*')" />

        @if($event && $event->attachments)
            <div class="mt-4">
                <h4 class="text-sm font-medium text-gray-900">{{ __('Current Attachments') }}</h4>
                <ul class="mt-2 divide-y divide-gray-200">
                    @foreach($event->attachments as $attachment)
                        <li class="py-2 flex justify-between items-center">
                            <span class="text-sm text-gray-600">{{ basename($attachment) }}</span>
                            <button type="button" 
                                    class="text-sm text-red-600 hover:text-red-900"
                                    onclick="if(confirm('{{ __('Are you sure you want to remove this attachment?') }}')) {
                                        document.getElementById('remove-attachment-{{ $loop->index }}').submit();
                                    }">
                                {{ __('Remove') }}
                            </button>
                        </li>
                        <form id="remove-attachment-{{ $loop->index }}" 
                              action="{{ route('events.remove-attachment', ['event' => $event, 'attachment' => basename($attachment)]) }}" 
                              method="POST" 
                              class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const matchFields = document.getElementById('match-fields');
        const meetingFields = document.getElementById('meeting-fields');

        function toggleFields() {
            const selectedType = typeSelect.value;
            matchFields.style.display = selectedType === 'match' ? 'block' : 'none';
            meetingFields.style.display = selectedType === 'meeting' ? 'block' : 'none';

            // Toggle required attributes
            const opponentInput = document.getElementById('opponent');
            const venueSelect = document.getElementById('venue');
            const meetingLinkInput = document.getElementById('meeting_link');

            opponentInput.required = selectedType === 'match';
            venueSelect.required = selectedType === 'match';
            meetingLinkInput.required = selectedType === 'meeting';
        }

        typeSelect.addEventListener('change', toggleFields);
        toggleFields(); // Initial state
    });
</script>
@endpush 