@props(['sponsor' => null])

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Profile Picture -->
    <div class="col-span-2">
        <x-input-label for="profile_picture" :value="__('Profile Picture')" />
        <input id="profile_picture" name="profile_picture" type="file" accept="image/*" class="mt-1 block w-full" />
        @if($sponsor && $sponsor->profile_picture)
            <div class="mt-2">
                <img src="{{ asset('storage/' . $sponsor->profile_picture) }}" alt="Profile Picture" class="h-20 w-20 object-cover rounded-full">
            </div>
        @endif
        <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
    </div>

    <!-- Name -->
    <div class="md:col-span-2">
        <x-input-label for="name" :value="__('Company Name *')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $sponsor->name ?? '')" required autofocus autocomplete="name" />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <!-- Contact Person -->
    <div>
        <x-input-label for="contact_person" :value="__('Contact Person')" />
        <x-text-input id="contact_person" name="contact_person" type="text" class="mt-1 block w-full" 
            :value="old('contact_person', $sponsor?->contact_person)" />
        <x-input-error class="mt-2" :messages="$errors->get('contact_person')" />
    </div>

    <!-- Email -->
    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" 
            :value="old('email', $sponsor?->email)" />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    <!-- Phone -->
    <div>
        <x-input-label for="phone" :value="__('Phone')" />
        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" 
            :value="old('phone', $sponsor?->phone)" />
        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
    </div>

    <!-- WhatsApp Number -->
    <div>
        <x-input-label for="whatsapp_number" :value="__('WhatsApp Number')" />
        <x-text-input id="whatsapp_number" name="whatsapp_number" type="text" class="mt-1 block w-full" 
            :value="old('whatsapp_number', $sponsor?->whatsapp_number)" placeholder="Enter WhatsApp Number" />
        <x-input-error class="mt-2" :messages="$errors->get('whatsapp_number')" />
    </div>

    <!-- Address -->
    <div class="md:col-span-2">
        <x-input-label for="address" :value="__('Address')" />
        <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" 
            :value="old('address', $sponsor?->address)" />
        <x-input-error class="mt-2" :messages="$errors->get('address')" />
    </div>

    <!-- Sponsorship Type -->
    <div>
        <x-input-label for="sponsorship_type" :value="__('Sponsorship Type')" />
        <x-text-input id="sponsorship_type" name="sponsorship_type" type="text" class="mt-1 block w-full" 
            :value="old('sponsorship_type', $sponsor?->sponsorship_type)" />
        <x-input-error class="mt-2" :messages="$errors->get('sponsorship_type')" />
    </div>

    <!-- Sponsorship Amount -->
    <div>
        <x-input-label for="sponsorship_amount" :value="__('Sponsorship Amount (LKR) *')" />
        <x-text-input id="sponsorship_amount" name="sponsorship_amount" type="number" step="0.01" class="mt-1 block w-full" 
            :value="old('sponsorship_amount', $sponsor?->sponsorship_amount)" required />
        <x-input-error class="mt-2" :messages="$errors->get('sponsorship_amount')" />
    </div>

    <!-- Notes -->
    <div class="md:col-span-2">
        <x-input-label for="notes" :value="__('Additional Notes')" />
        <textarea id="notes" name="notes" rows="3" 
            class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring focus:ring-yellow-200 focus:ring-opacity-50 rounded-md shadow-sm"
            placeholder="Enter any additional notes about the sponsor">{{ old('notes', $sponsor?->notes) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('notes')" />
    </div>

    <!-- Sponsorship Start Date -->
    <div>
        <x-input-label for="sponsorship_start_date" :value="__('Sponsorship Start Date')" />
        <x-text-input id="sponsorship_start_date" name="sponsorship_start_date" type="date" class="mt-1 block w-full" 
            :value="old('sponsorship_start_date', $sponsor?->sponsorship_start_date?->format('Y-m-d'))" />
        <x-input-error class="mt-2" :messages="$errors->get('sponsorship_start_date')" />
    </div>

    <!-- Sponsorship End Date -->
    <div>
        <x-input-label for="sponsorship_end_date" :value="__('Sponsorship End Date')" />
        <x-text-input id="sponsorship_end_date" name="sponsorship_end_date" type="date" class="mt-1 block w-full" 
            :value="old('sponsorship_end_date', $sponsor?->sponsorship_end_date?->format('Y-m-d'))" />
        <x-input-error class="mt-2" :messages="$errors->get('sponsorship_end_date')" />
    </div>

    <!-- Status -->
    <div>
        <x-input-label for="status" :value="__('Status')" />
        <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring focus:ring-yellow-500 rounded-md shadow-sm">
            <option value="active" {{ old('status', $sponsor?->status) === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $sponsor?->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('status')" />
    </div>

    <!-- File Attachments -->
    <x-forms.attachments :model="$sponsor" />
</div> 