<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User Details') }}
            </h2>
            <div>
                <a href="{{ route('users.edit', $user) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mr-2">
                    Edit User
                </a>
                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to delete this user?')">
                        Delete User
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">User Information</h3>
                    <dl class="grid grid-cols-1 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-yellow-600">Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-yellow-600">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-yellow-600">Roles</dt>
                            <dd class="mt-1">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->roles as $role)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @endforeach
                                </div>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-yellow-600">Created At</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('Y-m-d H:i:s') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-yellow-600">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('Y-m-d H:i:s') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            @if($user->member || $user->staff || $user->player)
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Associated Records</h3>
                    <dl class="grid grid-cols-1 gap-4">
                        @if($user->member)
                        <div>
                            <dt class="text-sm font-medium text-yellow-600">Member</dt>
                            <dd class="mt-1">
                                <a href="{{ route('members.show', $user->member) }}" class="text-yellow-600 hover:text-yellow-900">
                                    {{ $user->member->membership_number }} - {{ $user->member->first_name }} {{ $user->member->last_name }}
                                </a>
                            </dd>
                        </div>
                        @endif

                        @if($user->staff)
                        <div>
                            <dt class="text-sm font-medium text-yellow-600">Staff</dt>
                            <dd class="mt-1">
                                <a href="{{ route('staff.show', $user->staff) }}" class="text-yellow-600 hover:text-yellow-900">
                                    {{ $user->staff->employee_id }} - {{ $user->staff->first_name }} {{ $user->staff->last_name }}
                                </a>
                            </dd>
                        </div>
                        @endif

                        @if($user->player)
                        <div>
                            <dt class="text-sm font-medium text-yellow-600">Player</dt>
                            <dd class="mt-1">
                                <a href="{{ route('players.show', $user->player) }}" class="text-yellow-600 hover:text-yellow-900">
                                    {{ $user->player->first_name }} {{ $user->player->last_name }} - {{ $user->player->position }}
                                </a>
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout> 