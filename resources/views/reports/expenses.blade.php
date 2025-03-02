<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Expense Report') }}
            </h2>
            <div class="flex space-x-4">
                <form action="{{ route('reports.export.pdf') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="report_type" value="expenses">
                    <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                    <input type="hidden" name="category" value="{{ request('category') }}">
                    <input type="hidden" name="payment_method" value="{{ request('payment_method') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <input type="hidden" name="bank_account_id" value="{{ request('bank_account_id') }}">
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export PDF
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Total Expenses -->
                <div class="bg-red-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-red-600">Total Expenses</div>
                        <div class="mt-2 text-2xl font-bold text-red-900">
                            LKR {{ number_format($totalExpenses, 2) }}
                        </div>
                    </div>
                </div>

                <!-- Monthly Expenses -->
                <div class="bg-red-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-red-600">Monthly Expenses</div>
                        <div class="mt-2 text-2xl font-bold text-red-900">
                            LKR {{ number_format($monthlyExpenses, 2) }}
                        </div>
                    </div>
                </div>

                <!-- Yearly Expenses -->
                <div class="bg-red-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-red-600">Yearly Expenses</div>
                        <div class="mt-2 text-2xl font-bold text-red-900">
                            LKR {{ number_format($yearlyExpenses, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <x-report-filters 
                :action="route('reports.expenses')"
                :filters="[
                    'date' => true,
                    'category' => true,
                    'payment_method' => true,
                    'status' => true,
                    'bank_account' => true,
                ]"
                :bankAccounts="$bankAccounts"
                :categories="$categories"
            />

            <!-- Expense Transactions Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Transaction Number
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Category
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Amount
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Related To
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Payment Method
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Bank Account
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $transaction->transaction_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $transaction->transaction_date->format('Y-m-d') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $transaction->category }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">
                                            LKR {{ number_format($transaction->amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($transaction->transactionable)
                                                <div class="flex flex-col">
                                                    <span class="font-medium text-gray-900">
                                                        @switch($transaction->transactionable_type)
                                                            @case('App\Models\Player')
                                                            @case('App\Models\Staff')
                                                            @case('App\Models\Member')
                                                                {{ $transaction->transactionable->first_name }} {{ $transaction->transactionable->last_name }}
                                                                @break
                                                            @case('App\Models\Sponsor')
                                                                {{ $transaction->transactionable->name }}
                                                                @break
                                                        @endswitch
                                                    </span>
                                                    <span class="text-xs text-gray-500">
                                                        {{ class_basename($transaction->transactionable_type) }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($transaction->status === 'completed') bg-green-100 text-green-800
                                                @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $transaction->bankAccount->bank_name }} - {{ $transaction->bankAccount->account_number }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No expense transactions found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 