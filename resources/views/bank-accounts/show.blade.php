<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Bank Account Details') }}
            </h2>
            <div>
                <a href="{{ route('bank-accounts.edit', $bankAccount) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mr-2">
                    Edit Account
                </a>
                <form action="{{ route('bank-accounts.destroy', $bankAccount) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to delete this account?')">
                        Delete Account
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Account Information -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>
                        <dl class="grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-yellow-600">Account Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bankAccount->account_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-yellow-600">Account Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bankAccount->account_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-yellow-600">Bank Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bankAccount->bank_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-yellow-600">Branch Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bankAccount->branch_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-yellow-600">Swift Code</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $bankAccount->swift_code ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-yellow-600">Account Type</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $bankAccount->account_type)) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-yellow-600">Current Balance</dt>
                                <dd class="mt-1 text-sm text-gray-900">LKR {{ number_format($bankAccount->current_balance, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-yellow-600">Status</dt>
                                <dd class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($bankAccount->status === 'active') bg-green-100 text-green-800
                                        @elseif($bankAccount->status === 'inactive') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($bankAccount->status) }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
                            <a href="{{ route('financial-transactions.create', ['bank_account_id' => $bankAccount->id]) }}" class="text-yellow-600 hover:text-yellow-900">Add Transaction →</a>
                        </div>
                        @if($bankAccount->financialTransactions->isNotEmpty())
                            <div class="space-y-4">
                                @foreach($bankAccount->financialTransactions as $transaction)
                                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $transaction->description ?: 'N/A' }}</p>
                                            <p class="text-xs text-gray-500">{{ $transaction->transaction_date->format('Y-m-d') }}</p>
                                            <p class="text-xs text-gray-500">{{ $transaction->category }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium @if($transaction->type === 'income') text-green-600 @else text-red-600 @endif">
                                                {{ $transaction->type === 'income' ? '+' : '-' }} LKR {{ number_format($transaction->amount, 2) }}
                                            </p>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($transaction->status === 'completed') bg-green-100 text-green-800
                                                @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="mt-4 text-right">
                                    <a href="{{ route('financial-transactions.index', ['bank_account_id' => $bankAccount->id]) }}" class="text-sm text-yellow-600 hover:text-yellow-900">View All Transactions →</a>
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No transactions found.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- Interbank Transfers -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Interbank Transfers</h3>
                            <a href="{{ route('interbank-transfers.create', ['from_account_id' => $bankAccount->id]) }}" class="text-yellow-600 hover:text-yellow-900">New Transfer →</a>
                        </div>
                        @php
                            $incomingTransfers = $bankAccount->incomingTransfers()->latest('transfer_date')->take(5)->get();
                            $outgoingTransfers = $bankAccount->outgoingTransfers()->latest('transfer_date')->take(5)->get();
                            $transfers = $incomingTransfers->concat($outgoingTransfers)->sortByDesc('transfer_date')->take(5);
                        @endphp
                        @if($transfers->count() > 0)
                            <div class="space-y-4">
                                @foreach($transfers as $transfer)
                                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                @if($transfer->from_account_id === $bankAccount->id)
                                                    To: {{ $transfer->toAccount->bank_name }} - {{ $transfer->toAccount->account_number }}
                                                @else
                                                    From: {{ $transfer->fromAccount->bank_name }} - {{ $transfer->fromAccount->account_number }}
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $transfer->transfer_date->format('Y-m-d') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium @if($transfer->from_account_id === $bankAccount->id) text-red-600 @else text-green-600 @endif">
                                                {{ $transfer->from_account_id === $bankAccount->id ? '-' : '+' }} {{ $bankAccount->currency }} {{ number_format($transfer->amount, 2) }}
                                            </p>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($transfer->status === 'completed') bg-green-100 text-green-800
                                                @elseif($transfer->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($transfer->status === 'failed') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($transfer->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4 text-right">
                                <a href="{{ route('interbank-transfers.index', ['account_id' => $bankAccount->id]) }}" class="text-sm text-yellow-600 hover:text-yellow-900">View All Transfers →</a>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No interbank transfers found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 