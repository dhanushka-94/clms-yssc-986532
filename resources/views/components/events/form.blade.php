@props(['event' => null, 'players' => [], 'staff' => [], 'members' => [], 'selectedPlayers' => [], 'selectedStaff' => [], 'selectedMembers' => []])

{{-- Debug information --}}
@php
    \Log::info('Form Component Data:', [
        'players_count' => count($players),
        'staff_count' => count($staff),
        'members_count' => count($members)
    ]);
@endphp

<!-- Debug output -->
<div class="bg-yellow-100 p-4 mb-4">
    <p>Players Count: {{ count($players) }}</p>
    <p>Staff Count: {{ count($staff) }}</p>
    <p>Members Count: {{ count($members) }}</p>
</div>

<div class="space-y-6">
    <!-- Title -->
    <div>
        <x-input-label for="title" :value="__('Title *')" />
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
        <x-input-label for="type" :value="__('Event Type *')" />
        <select id="type" name="type" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" required>
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
            <x-input-label for="start_time" :value="__('Start Time *')" />
            <x-text-input id="start_time" name="start_time" type="datetime-local" class="mt-1 block w-full" :value="old('start_time', $event?->start_time?->format('Y-m-d\TH:i'))" required />
            <x-input-error class="mt-2" :messages="$errors->get('start_time')" />
        </div>

        <div>
            <x-input-label for="end_time" :value="__('End Time *')" />
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
            <x-input-label for="opponent" :value="__('Opponent *')" />
            <x-text-input id="opponent" name="opponent" type="text" class="mt-1 block w-full" :value="old('opponent', $event?->opponent)" />
            <x-input-error class="mt-2" :messages="$errors->get('opponent')" />
        </div>

        <div>
            <x-input-label for="venue" :value="__('Venue *')" />
            <select id="venue" name="venue" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm">
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
            <p class="mt-1 text-sm text-gray-500">Optional: Add a link for virtual meetings</p>
            <x-input-error class="mt-2" :messages="$errors->get('meeting_link')" />
        </div>
    </div>

    <!-- Status -->
    <div>
        <x-input-label for="status" :value="__('Status *')" />
        <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" required>
            <option value="scheduled" {{ old('status', $event?->status) === 'scheduled' ? 'selected' : '' }}>{{ __('Scheduled') }}</option>
            <option value="in_progress" {{ old('status', $event?->status) === 'in_progress' ? 'selected' : '' }}>{{ __('In Progress') }}</option>
            <option value="completed" {{ old('status', $event?->status) === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
            <option value="cancelled" {{ old('status', $event?->status) === 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('status')" />
    </div>

    <!-- Add Required Fields Note -->
    <div class="text-sm text-gray-500 mt-4">
        <p>Fields marked with an asterisk (*) are required.</p>
    </div>

    <!-- Attendees Selection -->
    <div class="space-y-6">
        <h3 class="text-lg font-medium text-gray-900">{{ __('Select Attendees') }}</h3>
        
        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button type="button"
                    class="tab-button border-yellow-500 text-yellow-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    data-target="players-tab">
                    Players
                </button>
                <button type="button"
                    class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    data-target="staff-tab">
                    Staff
                </button>
                <button type="button"
                    class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    data-target="members-tab">
                    Members
                </button>
            </nav>
        </div>

        <!-- Search Box -->
        <div class="relative">
            <input type="text" id="attendee-search" 
                class="w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm pl-10" 
                placeholder="Search attendees...">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>

        <!-- Tab Contents -->
        <div class="tab-content" id="players-tab">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($players as $player)
                    <label class="relative flex items-start py-3 px-4 border rounded-lg hover:bg-gray-50">
                        <div class="min-w-0 flex-1 text-sm">
                            <div class="font-medium text-gray-700">
                                {{ $player->first_name }} {{ $player->last_name }}
                            </div>
                            <div class="text-gray-500">
                                #{{ $player->jersey_number ?? 'N/A' }} - {{ ucfirst($player->position) }}
                            </div>
                        </div>
                        <div class="ml-3 flex items-center h-5">
                            <input type="checkbox" 
                                name="players[]" 
                                value="{{ $player->id }}"
                                {{ in_array($player->id, old('players', $selectedPlayers ?? [])) ? 'checked' : '' }}
                                class="focus:ring-yellow-500 h-4 w-4 text-yellow-600 border-gray-300 rounded">
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="tab-content hidden" id="staff-tab">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($staff as $staffMember)
                    <label class="relative flex items-start py-3 px-4 border rounded-lg hover:bg-gray-50">
                        <div class="min-w-0 flex-1 text-sm">
                            <div class="font-medium text-gray-700">
                                {{ $staffMember->first_name }} {{ $staffMember->last_name }}
                            </div>
                            <div class="text-gray-500">
                                {{ ucfirst($staffMember->role) }}
                            </div>
                        </div>
                        <div class="ml-3 flex items-center h-5">
                            <input type="checkbox" 
                                name="staff[]" 
                                value="{{ $staffMember->id }}"
                                {{ in_array($staffMember->id, old('staff', $selectedStaff ?? [])) ? 'checked' : '' }}
                                class="focus:ring-yellow-500 h-4 w-4 text-yellow-600 border-gray-300 rounded">
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="tab-content hidden" id="members-tab">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($members as $member)
                    <label class="relative flex items-start py-3 px-4 border rounded-lg hover:bg-gray-50">
                        <div class="min-w-0 flex-1 text-sm">
                            <div class="font-medium text-gray-700">
                                {{ $member->first_name }} {{ $member->last_name }}
                            </div>
                            <div class="text-gray-500">
                                {{ ucfirst($member->membership_type) }}
                            </div>
                        </div>
                        <div class="ml-3 flex items-center h-5">
                            <input type="checkbox" 
                                name="members[]" 
                                value="{{ $member->id }}"
                                {{ in_array($member->id, old('members', $selectedMembers ?? [])) ? 'checked' : '' }}
                                class="focus:ring-yellow-500 h-4 w-4 text-yellow-600 border-gray-300 rounded">
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
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
        // Event Type Toggle
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

            if (opponentInput) opponentInput.required = selectedType === 'match';
            if (venueSelect) venueSelect.required = selectedType === 'match';
            if (meetingLinkInput) meetingLinkInput.required = false;
        }

        typeSelect.addEventListener('change', toggleFields);
        toggleFields();

        // Attendee Tab Switching
        const tabs = document.querySelectorAll('.tab-button');
        const contents = document.querySelectorAll('.tab-content');

        function switchTab(targetId) {
            // Remove active classes from all tabs
            tabs.forEach(t => {
                t.classList.remove('border-yellow-500', 'text-yellow-600');
                t.classList.add('border-transparent', 'text-gray-500');
            });

            // Add active classes to clicked tab
            const activeTab = document.querySelector(`[data-target="${targetId}"]`);
            if (activeTab) {
                activeTab.classList.remove('border-transparent', 'text-gray-500');
                activeTab.classList.add('border-yellow-500', 'text-yellow-600');
            }

            // Hide all contents
            contents.forEach(content => {
                content.classList.add('hidden');
            });

            // Show selected content
            const activeContent = document.getElementById(targetId);
            if (activeContent) {
                activeContent.classList.remove('hidden');
            }
        }

        // Add click event listeners to tabs
        tabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = tab.getAttribute('data-target');
                switchTab(targetId);
            });
        });

        // Initialize with first tab active
        switchTab('players-tab');

        // Search functionality
        const searchInput = document.getElementById('attendee-search');
        const allLabels = document.querySelectorAll('.tab-content label');

        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();

            allLabels.forEach(label => {
                const text = label.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    label.style.display = '';
                } else {
                    label.style.display = 'none';
                }
            });
        });
    });
</script>
@endpush 