<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                {{ __('Transaction Report') }}
            </h2>
            <div class="flex space-x-4">
                <button onclick="window.print()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Report
                </button>
                <form action="{{ route('reports.export.pdf') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="report_type" value="transactions">
                    <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                    <input type="hidden" name="type" value="{{ request('type') }}">
                    <input type="hidden" name="category" value="{{ request('category') }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    <input type="hidden" name="payment_method" value="{{ request('payment_method') }}">
                    <input type="hidden" name="bank_account_id" value="{{ request('bank_account_id') }}">
                    <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-700 font-medium py-2 px-4 rounded inline-flex items-center">
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
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Total Transactions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Transactions</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $transactions->total() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Income -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 mr-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Income</p>
                            <p class="text-lg font-semibold text-green-600">LKR {{ number_format($totalIncome, 2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Expenses -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 mr-4">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Expenses</p>
                            <p class="text-lg font-semibold text-red-600">LKR {{ number_format($totalExpenses, 2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Pending Transactions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 mr-4">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Pending Transactions</p>
                            <p class="text-lg font-semibold text-yellow-600">{{ $pendingTransactions }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Advanced Filters -->
            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Advanced Filters
                    </h3>
                    <form action="{{ route('reports.transactions') }}" method="GET" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Date Range -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Date Range
                                </label>
                                <div class="flex space-x-2">
                                    <div class="relative flex-1">
                                        <input type="date" name="date_from" value="{{ request('date_from') }}" 
                                            class="form-input rounded-md shadow-sm block w-full" placeholder="From">
                                        <span class="text-xs text-gray-500 mt-1 absolute -bottom-5">From</span>
                                    </div>
                                    <div class="relative flex-1">
                                        <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                            class="form-input rounded-md shadow-sm block w-full" placeholder="To">
                                        <span class="text-xs text-gray-500 mt-1 absolute -bottom-5">To</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Transaction Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    Transaction Type
                                </label>
                                <select name="type" class="form-select rounded-md shadow-sm block w-full">
                                    <option value="">All Types</option>
                                    <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                                    <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                                </select>
                            </div>

                            <!-- Category -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    Category
                                </label>
                                <select name="category" class="form-select rounded-md shadow-sm block w-full">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ request('category') == $category->id ? 'selected' : '' }}
                                            class="text-{{ $category->color }}-600">
                                            {{ $category->name }} 
                                            <span class="text-gray-500">({{ ucfirst($category->type) }})</span>
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Status
                                </label>
                                <select name="status" class="form-select rounded-md shadow-sm block w-full">
                                    <option value="">All Statuses</option>
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    Payment Method
                                </label>
                                <select name="payment_method" class="form-select rounded-md shadow-sm block w-full">
                                    <option value="">All Methods</option>
                                    <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="check" {{ request('payment_method') === 'check' ? 'selected' : '' }}>Check</option>
                                    <option value="online" {{ request('payment_method') === 'online' ? 'selected' : '' }}>Online</option>
                                </select>
                            </div>

                            <!-- Bank Account -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                    </svg>
                                    Bank Account
                                </label>
                                <select name="bank_account_id" class="form-select rounded-md shadow-sm block w-full">
                                    <option value="">All Accounts</option>
                                    @foreach($bankAccounts as $account)
                                        <option value="{{ $account->id }}" {{ request('bank_account_id') == $account->id ? 'selected' : '' }}>
                                            {{ $account->bank_name }} - {{ $account->account_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="resetFilters()" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Reset Filters
                                </span>
                            </button>
                            <button type="submit" class="bg-yellow-500 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    Apply Filters
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Transaction List
                        </h3>
                        <p class="text-sm text-gray-600">
                            Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} transactions
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('transaction_number')">
                                        Transaction Number
                                        <span class="sort-icon ml-1">↕</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('date')">
                                        Date
                                        <span class="sort-icon ml-1">↕</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('type')">
                                        Type
                                        <span class="sort-icon ml-1">↕</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('category')">
                                        Category
                                        <span class="sort-icon ml-1">↕</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('amount')">
                                        Amount
                                        <span class="sort-icon ml-1">↕</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('status')">
                                        Status
                                        <span class="sort-icon ml-1">↕</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('payment_method')">
                                        Payment Method
                                        <span class="sort-icon ml-1">↕</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('bank_account')">
                                        Bank Account
                                        <span class="sort-icon ml-1">↕</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('related_to')">
                                        Related To
                                        <span class="sort-icon ml-1">↕</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($transactions as $transaction)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-yellow-600">
                                            {{ $transaction->transaction_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $transaction->transaction_date->format('Y-m-d') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $transaction->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    @if($transaction->type === 'income')
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                    @endif
                                                </svg>
                                                {{ ucfirst($transaction->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $transaction->category }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                            LKR {{ number_format($transaction->amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($transaction->status === 'completed') bg-green-100 text-green-800
                                                @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800 @endif">
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
                                            @if($transaction->transactionable)
                                                <div class="flex flex-col">
                                                    <span class="font-medium">
                                                        {{ $transaction->transactionable->first_name ?? '' }} 
                                                        {{ $transaction->transactionable->last_name ?? $transaction->transactionable->name ?? '' }}
                                                    </span>
                                                    <span class="text-xs text-gray-500">
                                                        {{ class_basename($transaction->transactionable_type) }}
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No transactions found.
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentSort = {
                column: null,
                direction: 'asc'
            };

            // Add click handlers to all sortable headers
            document.querySelectorAll('th[onclick]').forEach(header => {
                header.addEventListener('click', function() {
                    const column = this.getAttribute('onclick').match(/'([^']+)'/)[1];
                    handleSort(column);
                });
            });

            function handleSort(column) {
                const tbody = document.querySelector('tbody');
                const rows = Array.from(tbody.querySelectorAll('tr:not(.empty-row)'));
                const sortIcons = document.querySelectorAll('.sort-icon');

                // Reset all sort icons
                sortIcons.forEach(icon => icon.textContent = '↕');

                // Update sort direction
                if (currentSort.column === column) {
                    currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSort.column = column;
                    currentSort.direction = 'asc';
                }

                // Update sort icon for current column
                const currentHeader = document.querySelector(`th[onclick*="${column}"]`);
                const currentIcon = currentHeader.querySelector('.sort-icon');
                currentIcon.textContent = currentSort.direction === 'asc' ? '↑' : '↓';

                // Sort the rows
                rows.sort((a, b) => {
                    let aValue, bValue;

                    switch(column) {
                        case 'transaction_number':
                            aValue = a.cells[0].textContent.trim();
                            bValue = b.cells[0].textContent.trim();
                            break;
                        case 'date':
                            // Parse dates for proper comparison
                            aValue = new Date(a.cells[1].textContent.trim()).getTime();
                            bValue = new Date(b.cells[1].textContent.trim()).getTime();
                            return currentSort.direction === 'asc' ? aValue - bValue : bValue - aValue;
                        case 'type':
                            aValue = a.cells[2].textContent.trim();
                            bValue = b.cells[2].textContent.trim();
                            break;
                        case 'category':
                            aValue = a.cells[3].textContent.trim();
                            bValue = b.cells[3].textContent.trim();
                            break;
                        case 'amount':
                            // Remove currency symbol and commas, then parse as float
                            aValue = parseFloat(a.cells[4].textContent.replace(/[^0-9.-]+/g, '')) || 0;
                            bValue = parseFloat(b.cells[4].textContent.replace(/[^0-9.-]+/g, '')) || 0;
                            return currentSort.direction === 'asc' ? aValue - bValue : bValue - aValue;
                        case 'status':
                            aValue = a.cells[5].textContent.trim();
                            bValue = b.cells[5].textContent.trim();
                            break;
                        case 'payment_method':
                            aValue = a.cells[6].textContent.trim();
                            bValue = b.cells[6].textContent.trim();
                            break;
                        case 'bank_account':
                            aValue = a.cells[7].textContent.trim();
                            bValue = b.cells[7].textContent.trim();
                            break;
                        case 'related_to':
                            aValue = a.cells[8].textContent.trim();
                            bValue = b.cells[8].textContent.trim();
                            break;
                        default:
                            return 0;
                    }

                    // For non-numeric comparisons
                    if (column !== 'amount' && column !== 'date') {
                        return currentSort.direction === 'asc' ? 
                            aValue.localeCompare(bValue) : 
                            bValue.localeCompare(aValue);
                    }
                });

                // Remove existing rows
                rows.forEach(row => row.remove());

                // Add sorted rows
                rows.forEach(row => tbody.appendChild(row));

                // Save current sort state
                localStorage.setItem('transactionSort', JSON.stringify(currentSort));
            }

            // Restore previous sort if exists
            const savedSort = localStorage.getItem('transactionSort');
            if (savedSort) {
                const { column, direction } = JSON.parse(savedSort);
                currentSort = { column, direction };
                if (column) {
                    handleSort(column);
                }
            }
        });

        function resetFilters() {
            // Get all form inputs
            const form = document.querySelector('form[action="{{ route('reports.transactions') }}"]');
            const inputs = form.querySelectorAll('input, select');
            
            // Reset each input
            inputs.forEach(input => {
                if (input.type === 'date' || input.type === 'text') {
                    input.value = '';
                } else if (input.type === 'select-one') {
                    input.selectedIndex = 0;
                }
            });
            
            // Clear sort state
            localStorage.removeItem('transactionSort');
            
            // Submit the form
            form.submit();
        }
    </script>
    @endpush
</x-app-layout> 