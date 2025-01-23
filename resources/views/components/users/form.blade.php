@props(['user' => null, 'roles'])

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Profile Picture -->
    <div class="col-span-2">
        <x-input-label for="profile_picture" :value="__('Profile Picture')" />
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
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" 
            :value="old('name', $user?->name)" required />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <!-- Email -->
    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" 
            :value="old('email', $user?->email)" required />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    <!-- Password -->
    <div>
        <x-input-label for="password" :value="__('Password')" />
        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" 
            :required="!$user" />
        <x-input-error class="mt-2" :messages="$errors->get('password')" />
        @if($user)
            <p class="mt-1 text-sm text-gray-500">Leave blank to keep current password</p>
        @endif
    </div>

    <!-- Confirm Password -->
    <div>
        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" 
            :required="!$user" />
        <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
    </div>

    <!-- Roles -->
    <div>
        <x-input-label :value="__('Roles')" />
        <div class="mt-2 space-y-2">
            @foreach($roles as $role)
                <div class="flex items-center">
                    <input type="checkbox" id="role_{{ $role->id }}" name="roles[]" value="{{ $role->id }}"
                        class="rounded border-gray-300 text-yellow-600 shadow-sm focus:ring-yellow-500"
                        {{ in_array($role->id, old('roles', $user?->roles->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}>
                    <label for="role_{{ $role->id }}" class="ml-2 text-sm text-gray-600">
                        {{ ucfirst($role->name) }}
                    </label>
                </div>
            @endforeach
        </div>
        <x-input-error class="mt-2" :messages="$errors->get('roles')" />
    </div>
</div> 