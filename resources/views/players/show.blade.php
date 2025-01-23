<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Player Details') }}
            </h2>
            <div>
                <a href="{{ route('players.edit', $player) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded mr-2">
                    Edit Player
                </a>
                <form action="{{ route('players.destroy', $player) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded" 
                        onclick="return confirm('Are you sure you want to delete this player?')">
                        Delete Player
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Personal Information -->
                        <div class="bg-yellow-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4">Personal Information</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Player ID</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $player->player_id }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Full Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $player->first_name }} {{ $player->last_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">NIC</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $player->nic }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Date of Birth</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $player->date_of_birth->format('Y-m-d') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Contact Information -->
                        <div class="bg-yellow-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4">Contact Information</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Phone</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $player->phone }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Address</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $player->address }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $player->user->email }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Player Information -->
                        <div class="bg-yellow-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4">Player Information</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Position</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $player->position }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Jersey Number</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $player->jersey_number }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Joined Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $player->joined_date->format('Y-m-d') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Status</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($player->status === 'active') bg-green-100 text-green-800 
                                            @elseif($player->status === 'injured') bg-red-100 text-red-800
                                            @elseif($player->status === 'suspended') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($player->status) }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Contract Information -->
                        <div class="bg-yellow-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4">Contract Information</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Contract Amount</dt>
                                    <dd class="mt-1 text-sm text-gray-900">LKR {{ number_format($player->contract_amount, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Contract Period</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $player->contract_start_date->format('Y-m-d') }} to 
                                        {{ $player->contract_end_date->format('Y-m-d') }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Achievements</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $player->achievements ?? 'No achievements recorded' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Recent Transactions -->
                        <div class="md:col-span-2 bg-yellow-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4">Recent Transactions</h3>
                            @if($player->financialTransactions->count() > 0)
                                <div class="space-y-4">
                                    @foreach($player->financialTransactions->take(5) as $transaction)
                                        <div class="border-l-4 border-yellow-400 pl-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $transaction->description }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $transaction->transaction_date->format('Y-m-d') }} - 
                                                LKR {{ number_format($transaction->amount, 2) }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500">No transactions found.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 