<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Financial Transactions') }}
            </h2>
            <a href="{{ route('financial-transactions.create') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                Add New Transaction
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Total Income -->
                <div class="bg-green-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-green-600">Total Income</div>
                        <div class="mt-2 text-2xl font-bold text-green-900">
                            LKR {{ number_format($totalIncome, 2) }}
                        </div>
                    </div>
                </div>

                <!-- Total Expenses -->
                <div class="bg-red-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-red-600">Total Expenses</div>
                        <div class="mt-2 text-2xl font-bold text-red-900">
                            LKR {{ number_format($totalExpenses, 2) }}
                        </div>
                    </div>
                </div>

                <!-- Net Balance -->
                <div class="bg-blue-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-blue-600">Net Balance</div>
                        <div class="mt-2 text-2xl font-bold text-blue-900">
                            LKR {{ number_format($netBalance, 2) }}
                        </div>
                    </div>
                </div>

                <!-- Pending Transactions -->
                <div class="bg-yellow-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm font-medium text-yellow-600">Pending Transactions</div>
                        <div class="mt-2 text-2xl font-bold text-yellow-900">
                            {{ $pendingTransactions }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Search and Filter -->
                    <div class="mb-4 flex flex-wrap gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <input type="text" id="search" placeholder="Search transactions..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                        </div>
                        <div class="min-w-[150px]">
                            <select id="type-filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                <option value="">All Types</option>
                                <option value="income">Income</option>
                                <option value="expense">Expense</option>
                            </select>
                        </div>
                        <div class="min-w-[150px]">
                            <select id="category-filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                <option value="">All Categories</option>
                                <option value="sponsorship">Sponsorship</option>
                                <option value="salary">Salary</option>
                                <option value="allowance">Allowance</option>
                                <option value="equipment">Equipment</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="min-w-[150px]">
                            <select id="status-filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                <option value="">All Status</option>
                                <option value="completed">Completed</option>
                                <option value="pending">Pending</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <!-- Transactions Table -->
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
                                        Type
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Category
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Amount
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
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Related To
                                    </th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $transaction->transaction_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $transaction->transaction_date->format('Y-m-d') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transaction->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $transaction->category }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="{{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                            LKR {{ number_format($transaction->amount, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $transaction->transactionable_type ? $transaction->transactionable_name : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 space-x-3">
                                        <a href="{{ route('financial-transactions.show', $transaction) }}" class="text-yellow-600 hover:text-yellow-900">View</a>
                                        <a href="{{ route('financial-transactions.edit', $transaction) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                        <form action="{{ route('financial-transactions.destroy', $transaction) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this transaction?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
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