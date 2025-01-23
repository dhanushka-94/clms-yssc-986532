@props(['staff' => null])

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- First Name -->
    <div>
        <x-input-label for="first_name" :value="__('First Name')" />
        <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" 
            :value="old('first_name', $staff?->first_name)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
    </div>

    <!-- Last Name -->
    <div>
        <x-input-label for="last_name" :value="__('Last Name')" />
        <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" 
            :value="old('last_name', $staff?->last_name)" required />
        <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
    </div>

    <!-- NIC -->
    <div>
        <x-input-label for="nic" :value="__('NIC')" />
        <x-text-input id="nic" name="nic" type="text" class="mt-1 block w-full" 
            :value="old('nic', $staff?->nic)" required />
        <x-input-error class="mt-2" :messages="$errors->get('nic')" />
    </div>

    <!-- Phone -->
    <div>
        <x-input-label for="phone" :value="__('Phone')" />
        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" 
            :value="old('phone', $staff?->phone)" required />
        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
    </div>

    <!-- Address -->
    <div class="md:col-span-2">
        <x-input-label for="address" :value="__('Address')" />
        <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" 
            :value="old('address', $staff?->address)" required />
        <x-input-error class="mt-2" :messages="$errors->get('address')" />
    </div>

    <!-- Position -->
    <div>
        <x-input-label for="position" :value="__('Position')" />
        <x-text-input id="position" name="position" type="text" class="mt-1 block w-full" 
            :value="old('position', $staff?->position)" required />
        <x-input-error class="mt-2" :messages="$errors->get('position')" />
    </div>

    <!-- Date of Birth -->
    <div>
        <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
        <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full" 
            :value="old('date_of_birth', $staff?->date_of_birth?->format('Y-m-d'))" required />
        <x-input-error class="mt-2" :messages="$errors->get('date_of_birth')" />
    </div>

    <!-- Joined Date -->
    <div>
        <x-input-label for="joined_date" :value="__('Joined Date')" />
        <x-text-input id="joined_date" name="joined_date" type="date" class="mt-1 block w-full" 
            :value="old('joined_date', $staff?->joined_date?->format('Y-m-d'))" required />
        <x-input-error class="mt-2" :messages="$errors->get('joined_date')" />
    </div>

    <!-- Salary -->
    <div>
        <x-input-label for="salary" :value="__('Salary (LKR)')" />
        <x-text-input id="salary" name="salary" type="number" step="0.01" class="mt-1 block w-full" 
            :value="old('salary', $staff?->salary)" required />
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

    @if(!$staff)
    <!-- Email (Only for new staff) -->
    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    <!-- Password (Only for new staff) -->
    <div>
        <x-input-label for="password" :value="__('Password')" />
        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
        <x-input-error class="mt-2" :messages="$errors->get('password')" />
    </div>
    @endif
</div> 