<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Transfer Details') }}
            </h2>
            <div>
                <a href="{{ route('interbank-transfers.edit', $interbankTransfer->transfer_number) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mr-2">
                    Edit Transfer
                </a>
                <form action="{{ route('interbank-transfers.destroy', $interbankTransfer->transfer_number) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to delete this transfer?')">
                        Delete Transfer
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
                        <!-- Transfer Information -->
                        <div class="col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Transfer Information</h3>
                            <div class="bg-gray-50 rounded-lg p-4 grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Transfer Number</p>
                                    <p class="mt-1">{{ $interbankTransfer->transfer_number }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Transfer Date</p>
                                    <p class="mt-1">{{ $interbankTransfer->transfer_date->format('Y-m-d') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Amount</p>
                                    <p class="mt-1">{{ $interbankTransfer->fromAccount->currency }} {{ number_format($interbankTransfer->amount, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Status</p>
                                    <p class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($interbankTransfer->status === 'completed') bg-green-100 text-green-800
                                            @elseif($interbankTransfer->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($interbankTransfer->status === 'failed') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($interbankTransfer->status) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Source Account -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Source Account</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm font-medium text-gray-500">Bank Name</p>
                                <p class="mt-1">{{ $interbankTransfer->fromAccount->bank_name }}</p>
                                <p class="mt-3 text-sm font-medium text-gray-500">Account Number</p>
                                <p class="mt-1">{{ $interbankTransfer->fromAccount->account_number }}</p>
                                <p class="mt-3 text-sm font-medium text-gray-500">Current Balance</p>
                                <p class="mt-1">{{ $interbankTransfer->fromAccount->currency }} {{ number_format($interbankTransfer->fromAccount->current_balance, 2) }}</p>
                            </div>
                        </div>

                        <!-- Destination Account -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Destination Account</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm font-medium text-gray-500">Bank Name</p>
                                <p class="mt-1">{{ $interbankTransfer->toAccount->bank_name }}</p>
                                <p class="mt-3 text-sm font-medium text-gray-500">Account Number</p>
                                <p class="mt-1">{{ $interbankTransfer->toAccount->account_number }}</p>
                                <p class="mt-3 text-sm font-medium text-gray-500">Current Balance</p>
                                <p class="mt-1">{{ $interbankTransfer->toAccount->currency }} {{ number_format($interbankTransfer->toAccount->current_balance, 2) }}</p>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Description</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p>{{ $interbankTransfer->description }}</p>
                            </div>
                        </div>

                        <!-- Attachments -->
                        @if($interbankTransfer->attachments)
                        <div class="col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Attachments</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($interbankTransfer->attachments as $path)
                                        <div class="flex items-center justify-between p-2 bg-white rounded-lg shadow">
                                            <span class="text-sm text-gray-600">{{ basename($path) }}</span>
                                            <a href="{{ asset('storage/' . $path) }}" target="_blank" 
                                                class="text-yellow-600 hover:text-yellow-900">View</a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 