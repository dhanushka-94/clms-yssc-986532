@props(['staff' => null])

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Profile Picture -->
    <div class="col-span-2">
        <x-input-label for="profile_picture" :value="__('Profile Picture')" />
        <p class="text-sm text-gray-500 mb-2">Allowed formats: JPEG, PNG, JPG, GIF. Maximum size: 2MB</p>
        <input id="profile_picture" name="profile_picture" type="file" accept="image/*" class="mt-1 block w-full" />
        @if($staff && $staff->profile_picture)
            <div class="mt-2">
                <img src="{{ asset('storage/' . $staff->profile_picture) }}" alt="Profile Picture" class="h-20 w-20 object-cover rounded-full">
            </div>
        @endif
        <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
    </div>

    <!-- First Name -->
    <div>
        <x-input-label for="first_name" :value="__('First Name *')" />
        <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" 
            :value="old('first_name', $staff?->first_name)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
    </div>

    <!-- Last Name -->
    <div>
        <x-input-label for="last_name" :value="__('Last Name *')" />
        <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" 
            :value="old('last_name', $staff?->last_name)" required />
        <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
    </div>

    <!-- NIC -->
    <div>
        <x-input-label for="nic" :value="__('NIC')" />
        <x-text-input id="nic" name="nic" type="text" class="mt-1 block w-full" 
            :value="old('nic', $staff?->nic)" />
        <x-input-error class="mt-2" :messages="$errors->get('nic')" />
    </div>

    <!-- Phone -->
    <div>
        <x-input-label for="phone" :value="__('Phone')" />
        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" 
            :value="old('phone', $staff?->phone)" />
        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
    </div>

    <!-- WhatsApp Number -->
    <div>
        <x-input-label for="whatsapp_number" :value="__('WhatsApp Number')" />
        <x-text-input id="whatsapp_number" name="whatsapp_number" type="text" class="mt-1 block w-full" 
            :value="old('whatsapp_number', $staff?->whatsapp_number)" placeholder="Enter WhatsApp Number" />
        <x-input-error class="mt-2" :messages="$errors->get('whatsapp_number')" />
    </div>

    <!-- Address -->
    <div class="md:col-span-2">
        <x-input-label for="address" :value="__('Address')" />
        <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" 
            :value="old('address', $staff?->address)" />
        <x-input-error class="mt-2" :messages="$errors->get('address')" />
    </div>

    <!-- Designation -->
    <div>
        <x-input-label for="role" :value="__('Designation *')" />
        <select id="role" name="role" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" required>
            <option value="coach" {{ old('role', $staff?->role) === 'coach' ? 'selected' : '' }}>Coach</option>
            <option value="assistant_coach" {{ old('role', $staff?->role) === 'assistant_coach' ? 'selected' : '' }}>Assistant Coach</option>
            <option value="physiotherapist" {{ old('role', $staff?->role) === 'physiotherapist' ? 'selected' : '' }}>Physiotherapist</option>
            <option value="manager" {{ old('role', $staff?->role) === 'manager' ? 'selected' : '' }}>Manager</option>
            <option value="staff" {{ old('role', $staff?->role) === 'staff' ? 'selected' : '' }}>Staff</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('role')" />
    </div>

    <!-- Date of Birth -->
    <div>
        <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
        <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full" 
            :value="old('date_of_birth', $staff?->date_of_birth?->format('Y-m-d'))" />
        <x-input-error class="mt-2" :messages="$errors->get('date_of_birth')" />
    </div>

    <!-- Joined Date -->
    <div>
        <x-input-label for="joined_date" :value="__('Joined Date')" />
        <x-text-input id="joined_date" name="joined_date" type="date" class="mt-1 block w-full" 
            :value="old('joined_date', $staff?->joined_date?->format('Y-m-d'))" />
        <x-input-error class="mt-2" :messages="$errors->get('joined_date')" />
    </div>

    <!-- Contract Start Date -->
    <div>
        <x-input-label for="contract_start_date" :value="__('Contract Start Date')" />
        <x-text-input id="contract_start_date" name="contract_start_date" type="date" class="mt-1 block w-full" 
            :value="old('contract_start_date', $staff?->contract_start_date?->format('Y-m-d'))" />
        <x-input-error class="mt-2" :messages="$errors->get('contract_start_date')" />
    </div>

    <!-- Contract End Date -->
    <div>
        <x-input-label for="contract_end_date" :value="__('Contract End Date')" />
        <x-text-input id="contract_end_date" name="contract_end_date" type="date" class="mt-1 block w-full" 
            :value="old('contract_end_date', $staff?->contract_end_date?->format('Y-m-d'))" />
        <x-input-error class="mt-2" :messages="$errors->get('contract_end_date')" />
    </div>

    <!-- Salary -->
    <div>
        <x-input-label for="salary" :value="__('Salary (LKR)')" />
        <x-text-input id="salary" name="salary" type="number" step="0.01" class="mt-1 block w-full" 
            :value="old('salary', $staff?->salary)" />
        <x-input-error class="mt-2" :messages="$errors->get('salary')" />
    </div>

    <!-- Status -->
    <div>
        <x-input-label for="status" :value="__('Status')" />
        <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm">
            <option value="active" {{ old('status', $staff?->status) === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $staff?->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="terminated" {{ old('status', $staff?->status) === 'terminated' ? 'selected' : '' }}>Terminated</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('status')" />
    </div>

    <!-- File Attachments -->
    <div class="col-span-2">
        <x-forms.attachments :model="$staff" />
    </div>

    <!-- Required Fields Note -->
    <div class="col-span-2 mt-4">
        <p class="text-sm text-gray-500">* Required fields</p>
    </div>

    @if(config('club.features.staff_login'))
        @if(!$staff)
        <!-- Email (Only for new staff) -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <!-- Password (Only for new staff) -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
            <x-input-error class="mt-2" :messages="$errors->get('password')" />
        </div>

        <!-- Password Confirmation -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" />
            <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
        </div>
        @endif
    @endif
</div> 