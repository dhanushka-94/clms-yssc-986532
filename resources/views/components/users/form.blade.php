@props(['user' => null, 'roles'])

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Profile Picture -->
    <div class="col-span-2">
        <x-input-label for="profile_picture" :value="__('Profile Picture')" />
        <p class="text-sm text-gray-500 mb-2">Allowed formats: JPEG, PNG, JPG, GIF. Maximum size: 1MB</p>
        <input id="profile_picture" name="profile_picture" type="file" accept="image/*" class="mt-1 block w-full" />
        @if($user && $user->profile_picture)
            <div class="mt-2">
                <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture" class="h-20 w-20 object-cover rounded-full">
            </div>
        @endif
        <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
    </div>

    <!-- Name -->
    <div>
        <x-input-label for="name" :value="__('Name *')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" 
            :value="old('name', $user?->name)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <!-- Email -->
    <div>
        <x-input-label for="email" :value="__('Email *')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" 
            :value="old('email', $user?->email)" required />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    <!-- Password -->
    <div>
        <x-input-label for="password" :value="__($user ? 'Password' : 'Password *')" />
        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" 
            :required="!$user" />
        <x-input-error class="mt-2" :messages="$errors->get('password')" />
        @if($user)
            <p class="mt-1 text-sm text-gray-500">Leave blank to keep current password</p>
        @else
            <p class="mt-1 text-sm text-gray-500">Minimum 8 characters</p>
        @endif
    </div>

    <!-- Confirm Password -->
    <div>
        <x-input-label for="password_confirmation" :value="__($user ? 'Confirm Password' : 'Confirm Password *')" />
        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" 
            :required="!$user" />
        <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
    </div>

    <!-- Roles -->
    <div class="col-span-2">
        <x-input-label :value="__('Roles *')" />
        <div class="mt-2 grid grid-cols-2 gap-4">
            @foreach($roles as $role)
                <label class="inline-flex items-center">
                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                        {{ (old('roles') && in_array($role->id, old('roles'))) || ($user && $user->roles->contains($role->id)) ? 'checked' : '' }}>
                    <span class="ms-2">{{ ucfirst($role->name) }}</span>
                </label>
            @endforeach
        </div>
        <x-input-error class="mt-2" :messages="$errors->get('roles')" />
    </div>

    <div class="col-span-2 mt-4">
        <p class="text-sm text-gray-500">* Required fields</p>
    </div>
</div> 