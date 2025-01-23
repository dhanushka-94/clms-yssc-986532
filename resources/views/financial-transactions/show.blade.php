<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Transaction Details') }}
            </h2>
            <div>
                <a href="{{ route('financial-transactions.edit', $transaction->transaction_number) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded mr-2">
                    Edit Transaction
                </a>
                <a href="{{ route('financial-transactions.download-receipt', $transaction->transaction_number) }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded mr-2">
                    Download Receipt
                </a>
                @if($transaction->type === 'income')
                    <a href="{{ route('financial-transactions.download-invoice', $transaction->transaction_number) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mr-2">
                        Download Invoice
                    </a>
                @endif
                <form action="{{ route('financial-transactions.destroy', $transaction->transaction_number) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded" 
                        onclick="return confirm('Are you sure you want to delete this transaction?')">
                        Delete Transaction
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
                        <!-- Transaction Information -->
                        <div class="bg-yellow-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4">Transaction Information</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Transaction Number</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $transaction->transaction_number }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Related To</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @if($transaction->transactionable)
                                            {{ class_basename($transaction->transactionable_type) }}: 
                                            @switch($transaction->transactionable_type)
                                                @case('App\Models\Player')
                                                @case('App\Models\Staff')
                                                @case('App\Models\Member')
                                                    {{ $transaction->transactionable->first_name }} {{ $transaction->transactionable->last_name }}
                                                    @break
                                                @case('App\Models\Sponsor')
                                                    {{ $transaction->transactionable->company_name }}
                                                    @break
                                            @endswitch
                                        @else
                                            N/A
                                        @endif
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Type</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $transaction->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Category</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $transaction->category->name }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Amount</dt>
                                    <dd class="mt-1 text-sm font-semibold {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                        LKR {{ number_format($transaction->amount, 2) }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $transaction->date->format('Y-m-d') }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($transaction->status === 'completed') bg-green-100 text-green-800
                                            @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Payment Details -->
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-blue-800 mb-4">Payment Details</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Bank Account</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $transaction->bankAccount->bank_name }} - {{ $transaction->bankAccount->account_number }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Reference Number</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $transaction->reference_number ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Additional Information -->
                        <div class="bg-green-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-green-800 mb-4">Additional Information</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $transaction->description }}</dd>
                                </div>

                                @if($transaction->attachments)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Attachments</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <ul class="list-disc list-inside">
                                            @foreach($transaction->attachments as $attachment)
                                                <li>
                                                    <a href="{{ Storage::url($attachment) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                                        {{ basename($attachment) }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 