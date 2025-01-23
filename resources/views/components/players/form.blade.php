@props(['player' => null])

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Profile Picture -->
    <div class="col-span-2">
        <x-input-label for="profile_picture" :value="__('Profile Picture')" />
        <input id="profile_picture" name="profile_picture" type="file" accept="image/*" class="mt-1 block w-full" />
        @if($player && $player->profile_picture)
            <div class="mt-2">
                <img src="{{ asset('storage/' . $player->profile_picture) }}" alt="Profile Picture" class="h-20 w-20 object-cover rounded-full">
            </div>
        @endif
        <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
    </div>

    <!-- First Name -->
    <div>
        <x-input-label for="first_name" :value="__('First Name *')" />
        <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" 
            :value="old('first_name', $player?->first_name)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
    </div>

    <!-- Last Name -->
    <div>
        <x-input-label for="last_name" :value="__('Last Name *')" />
        <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" 
            :value="old('last_name', $player?->last_name)" required />
        <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
    </div>

    <!-- NIC -->
    <div>
        <x-input-label for="nic" :value="__('NIC *')" />
        <x-text-input id="nic" name="nic" type="text" class="mt-1 block w-full" 
            :value="old('nic', $player?->nic)" required />
        <x-input-error class="mt-2" :messages="$errors->get('nic')" />
    </div>

    <!-- FFSL Number -->
    <div>
        <x-input-label for="ffsl_number" :value="__('FFSL Number')" />
        <x-text-input id="ffsl_number" name="ffsl_number" type="text" class="mt-1 block w-full" 
            :value="old('ffsl_number', $player?->ffsl_number)" placeholder="Enter FFSL Number" />
        <x-input-error class="mt-2" :messages="$errors->get('ffsl_number')" />
    </div>

    <!-- Phone -->
    <div>
        <x-input-label for="phone" :value="__('Phone *')" />
        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" 
            :value="old('phone', $player?->phone)" required />
        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
    </div>

    <!-- WhatsApp Number -->
    <div>
        <x-input-label for="whatsapp_number" :value="__('WhatsApp Number')" />
        <x-text-input id="whatsapp_number" name="whatsapp_number" type="text" class="mt-1 block w-full" 
            :value="old('whatsapp_number', $player?->whatsapp_number)" placeholder="Enter WhatsApp Number" />
        <x-input-error class="mt-2" :messages="$errors->get('whatsapp_number')" />
    </div>

    <!-- Address -->
    <div class="md:col-span-2">
        <x-input-label for="address" :value="__('Address *')" />
        <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" 
            :value="old('address', $player?->address)" required />
        <x-input-error class="mt-2" :messages="$errors->get('address')" />
    </div>

    <!-- Position -->
    <div>
        <x-input-label for="position" :value="__('Position *')" />
        <x-text-input id="position" name="position" type="text" class="mt-1 block w-full" 
            :value="old('position', $player?->position)" required />
        <x-input-error class="mt-2" :messages="$errors->get('position')" />
    </div>

    <!-- Jersey Number -->
    <div>
        <x-input-label for="jersey_number" :value="__('Jersey Number *')" />
        <x-text-input id="jersey_number" name="jersey_number" type="text" class="mt-1 block w-full" 
            :value="old('jersey_number', $player?->jersey_number)" required />
        <x-input-error class="mt-2" :messages="$errors->get('jersey_number')" />
    </div>

    <!-- Date of Birth -->
    <div>
        <x-input-label for="date_of_birth" :value="__('Date of Birth *')" />
        <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full" 
            :value="old('date_of_birth', $player?->date_of_birth?->format('Y-m-d'))" required />
        <x-input-error class="mt-2" :messages="$errors->get('date_of_birth')" />
    </div>

    <!-- Joined Date -->
    <div>
        <x-input-label for="joined_date" :value="__('Joined Date *')" />
        <x-text-input id="joined_date" name="joined_date" type="date" class="mt-1 block w-full" 
            :value="old('joined_date', $player?->joined_date?->format('Y-m-d'))" required />
        <x-input-error class="mt-2" :messages="$errors->get('joined_date')" />
    </div>

    <!-- Contract Amount -->
    <div>
        <x-input-label for="contract_amount" :value="__('Contract Amount (LKR) *')" />
        <x-text-input id="contract_amount" name="contract_amount" type="number" step="0.01" class="mt-1 block w-full" 
            :value="old('contract_amount', $player?->contract_amount)" required />
        <x-input-error class="mt-2" :messages="$errors->get('contract_amount')" />
    </div>

    <!-- Contract Start Date -->
    <div>
        <x-input-label for="contract_start_date" :value="__('Contract Start Date *')" />
        <x-text-input id="contract_start_date" name="contract_start_date" type="date" class="mt-1 block w-full" 
            :value="old('contract_start_date', $player?->contract_start_date?->format('Y-m-d'))" required />
        <x-input-error class="mt-2" :messages="$errors->get('contract_start_date')" />
    </div>

    <!-- Contract End Date -->
    <div>
        <x-input-label for="contract_end_date" :value="__('Contract End Date *')" />
        <x-text-input id="contract_end_date" name="contract_end_date" type="date" class="mt-1 block w-full" 
            :value="old('contract_end_date', $player?->contract_end_date?->format('Y-m-d'))" required />
        <x-input-error class="mt-2" :messages="$errors->get('contract_end_date')" />
    </div>

    <!-- Status -->
    <div>
        <x-input-label for="status" :value="__('Status *')" />
        <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" required>
            <option value="active" {{ old('status', $player?->status) === 'active' ? 'selected' : '' }}>Active</option>
            <option value="injured" {{ old('status', $player?->status) === 'injured' ? 'selected' : '' }}>Injured</option>
            <option value="suspended" {{ old('status', $player?->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
            <option value="inactive" {{ old('status', $player?->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('status')" />
    </div>

    <!-- Achievements -->
    <div class="md:col-span-2">
        <x-input-label for="achievements" :value="__('Achievements')" />
        <textarea id="achievements" name="achievements" rows="3" 
            class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm">{{ old('achievements', $player?->achievements) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('achievements')" />
    </div>

    <!-- File Attachments -->
    <x-forms.attachments :model="$player" />

    @if(config('club.features.player_login'))
        @if(!$player)
        <!-- Email (Only for new players) -->
        <div>
            <x-input-label for="email" :value="__('Email *')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <!-- Password (Only for new players) -->
        <div>
            <x-input-label for="password" :value="__('Password *')" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('password')" />
        </div>

        <!-- Password Confirmation -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password *')" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
        </div>
        @endif
    @endif
</div> 