@props(['member' => null])

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Profile Picture -->
    <div class="md:col-span-2">
        <x-input-label for="profile_picture" :value="__('Profile Picture')" />
        <div class="mt-1 flex items-center gap-x-6">
            <div class="flex-none">
                <!-- Preview container -->
                <div id="preview-container" class="h-24 w-24 rounded-full bg-yellow-100 flex items-center justify-center overflow-hidden">
                    @if($member && $member->profile_picture)
                        <img src="{{ asset('storage/' . $member->profile_picture) }}" 
                            alt="Current Profile Picture" 
                            class="h-full w-full object-cover" 
                            id="preview-image">
                    @else
                        <span class="text-yellow-800 font-bold text-2xl" id="preview-text">
                            {{ $member ? strtoupper(substr($member->first_name, 0, 1)) : 'UP' }}
                        </span>
                        <img src="" alt="Profile Preview" class="h-full w-full object-cover hidden" id="preview-image">
                    @endif
                </div>
            </div>
            <div class="flex-grow">
                <input type="file" 
                    id="profile_picture" 
                    name="profile_picture" 
                    class="mt-1 block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-md file:border-0
                    file:text-sm file:font-semibold
                    file:bg-yellow-50 file:text-yellow-700
                    hover:file:bg-yellow-100"
                    accept="image/*"
                    onchange="previewImage(this)">
                <div class="mt-2 text-sm text-gray-500">
                    Accepted formats: JPG, JPEG, PNG, GIF (Max size: 2MB)
                </div>
                <div id="validation-message" class="mt-2 text-sm hidden"></div>
                <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview-image');
            const previewText = document.getElementById('preview-text');
            const validationMessage = document.getElementById('validation-message');
            const maxSize = 2 * 1024 * 1024; // 2MB in bytes

            validationMessage.classList.remove('text-red-500', 'text-green-500');
            validationMessage.classList.add('hidden');

            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Validate file size
                if (file.size > maxSize) {
                    validationMessage.textContent = 'File size exceeds 2MB limit';
                    validationMessage.classList.remove('hidden');
                    validationMessage.classList.add('text-red-500');
                    input.value = '';
                    return;
                }

                // Validate file type
                const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                if (!validTypes.includes(file.type)) {
                    validationMessage.textContent = 'Invalid file type. Please upload JPG, JPEG, PNG, or GIF';
                    validationMessage.classList.remove('hidden');
                    validationMessage.classList.add('text-red-500');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    if (previewText) previewText.classList.add('hidden');
                    validationMessage.textContent = 'Valid image selected';
                    validationMessage.classList.remove('hidden');
                    validationMessage.classList.add('text-green-500');
                }
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.classList.add('hidden');
                if (previewText) previewText.classList.remove('hidden');
            }
        }
    </script>

    <!-- First Name -->
    <div>
        <x-input-label for="first_name" :value="__('First Name')" />
        <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" 
            :value="old('first_name', $member?->first_name)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
    </div>

    <!-- Last Name -->
    <div>
        <x-input-label for="last_name" :value="__('Last Name')" />
        <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" 
            :value="old('last_name', $member?->last_name)" required />
        <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
    </div>

    <!-- NIC -->
    <div>
        <x-input-label for="nic" :value="__('NIC')" />
        <x-text-input id="nic" name="nic" type="text" class="mt-1 block w-full" 
            :value="old('nic', $member?->nic)" required />
        <x-input-error class="mt-2" :messages="$errors->get('nic')" />
    </div>

    <!-- Phone -->
    <div>
        <x-input-label for="phone" :value="__('Phone')" />
        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" 
            :value="old('phone', $member?->phone)" required />
        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
    </div>

    <!-- Address -->
    <div class="md:col-span-2">
        <x-input-label for="address" :value="__('Address')" />
        <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" 
            :value="old('address', $member?->address)" required />
        <x-input-error class="mt-2" :messages="$errors->get('address')" />
    </div>

    <!-- Date of Birth -->
    <div>
        <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
        <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full" 
            :value="old('date_of_birth', $member?->date_of_birth?->format('Y-m-d'))" required />
        <x-input-error class="mt-2" :messages="$errors->get('date_of_birth')" />
    </div>

    <!-- Joined Date -->
    <div>
        <x-input-label for="joined_date" :value="__('Joined Date')" />
        <x-text-input id="joined_date" name="joined_date" type="date" class="mt-1 block w-full" 
            :value="old('joined_date', $member?->joined_date?->format('Y-m-d'))" required />
        <x-input-error class="mt-2" :messages="$errors->get('joined_date')" />
    </div>

    <!-- Membership Fee -->
    <div>
        <x-input-label for="membership_fee" :value="__('Membership Fee (LKR)')" />
        <x-text-input id="membership_fee" name="membership_fee" type="number" step="0.01" class="mt-1 block w-full" 
            :value="old('membership_fee', $member?->membership_fee)" required />
        <x-input-error class="mt-2" :messages="$errors->get('membership_fee')" />
    </div>

    <!-- Status -->
    <div>
        <x-input-label for="status" :value="__('Status')" />
        <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm">
            <option value="active" {{ old('status', $member?->status) === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $member?->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="suspended" {{ old('status', $member?->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('status')" />
    </div>

    @if(!$member)
    <!-- Email (Only for new members) -->
    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    <!-- Password (Only for new members) -->
    <div>
        <x-input-label for="password" :value="__('Password')" />
        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
        <x-input-error class="mt-2" :messages="$errors->get('password')" />
    </div>
    @endif
</div> 