<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                {{ __('Financial Reports') }}
            </h2>
            <div class="flex space-x-4">
                <button onclick="window.print()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Report
                </button>
                <button onclick="exportToPDF()" class="bg-red-100 hover:bg-red-200 text-red-700 font-medium py-2 px-4 rounded inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export PDF
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Total Income -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-full p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <div class="text-sm font-medium text-green-600">Total Income</div>
                                <div class="mt-1 text-2xl font-bold text-green-900">
                                    LKR {{ number_format($totalIncome, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Expenses -->
                <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-full p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <div class="text-sm font-medium text-red-600">Total Expenses</div>
                                <div class="mt-1 text-2xl font-bold text-red-900">
                                    LKR {{ number_format($totalExpenses, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Net Balance -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-full p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <div class="text-sm font-medium text-blue-600">Net Balance</div>
                                <div class="mt-1 text-2xl font-bold text-blue-900">
                                    LKR {{ number_format($netBalance, 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Types -->
            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Available Reports
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Transaction Report -->
                        <a href="{{ route('reports.transactions') }}" class="group block p-6 bg-white border border-gray-200 rounded-lg hover:bg-yellow-50 hover:border-yellow-200 transition-colors duration-300">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 bg-yellow-100 rounded-full p-3 group-hover:bg-yellow-200 transition-colors duration-300">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <h4 class="ml-4 text-lg font-semibold text-gray-900 group-hover:text-yellow-700">Transaction Report</h4>
                            </div>
                            <p class="text-sm text-gray-600">View detailed transaction history with advanced filtering options.</p>
                        </a>

                        <!-- Income Report -->
                        <a href="{{ route('reports.income') }}" class="group block p-6 bg-white border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-200 transition-colors duration-300">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 bg-green-100 rounded-full p-3 group-hover:bg-green-200 transition-colors duration-300">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </div>
                                <h4 class="ml-4 text-lg font-semibold text-gray-900 group-hover:text-green-700">Income Report</h4>
                            </div>
                            <p class="text-sm text-gray-600">Analyze income patterns and sources over time.</p>
                        </a>

                        <!-- Expense Report -->
                        <a href="{{ route('reports.expenses') }}" class="group block p-6 bg-white border border-gray-200 rounded-lg hover:bg-red-50 hover:border-red-200 transition-colors duration-300">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 bg-red-100 rounded-full p-3 group-hover:bg-red-200 transition-colors duration-300">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </div>
                                <h4 class="ml-4 text-lg font-semibold text-gray-900 group-hover:text-red-700">Expense Report</h4>
                            </div>
                            <p class="text-sm text-gray-600">Track and analyze expenses by category and time period.</p>
                        </a>

                        <!-- Category Analysis -->
                        <a href="/reports/category-summary" class="group block p-6 bg-white border border-gray-200 rounded-lg hover:bg-purple-50 hover:border-purple-200 transition-colors duration-300">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 bg-purple-100 rounded-full p-3 group-hover:bg-purple-200 transition-colors duration-300">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                </div>
                                <h4 class="ml-4 text-lg font-semibold text-gray-900 group-hover:text-purple-700">Category Analysis</h4>
                            </div>
                            <p class="text-sm text-gray-600">View transaction distribution across different categories.</p>
                        </a>

                        <!-- Entity Report -->
                        <a href="{{ route('reports.entities') }}" class="group block p-6 bg-white border border-gray-200 rounded-lg hover:bg-indigo-50 hover:border-indigo-200 transition-colors duration-300">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 bg-indigo-100 rounded-full p-3 group-hover:bg-indigo-200 transition-colors duration-300">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <h4 class="ml-4 text-lg font-semibold text-gray-900 group-hover:text-indigo-700">Entity Report</h4>
                            </div>
                            <p class="text-sm text-gray-600">Analyze transactions by players, staff, members, and sponsors.</p>
                        </a>

                        <!-- Bank Account Report -->
                        <a href="{{ route('reports.bank-accounts') }}" class="group block p-6 bg-white border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-200 transition-colors duration-300">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0 bg-blue-100 rounded-full p-3 group-hover:bg-blue-200 transition-colors duration-300">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                    </svg>
                                </div>
                                <h4 class="ml-4 text-lg font-semibold text-gray-900 group-hover:text-blue-700">Bank Account Report</h4>
                            </div>
                            <p class="text-sm text-gray-600">View bank account balances and transaction history.</p>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
                        <a href="{{ route('reports.transactions') }}" class="text-yellow-600 hover:text-yellow-900">View All →</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentTransactions as $transaction)
                                    <tr>
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
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 