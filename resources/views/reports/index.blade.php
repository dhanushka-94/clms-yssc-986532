<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Financial Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
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
            </div>

            <!-- Report Types -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Available Reports</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Transaction Report -->
                        <a href="{{ route('reports.transactions') }}" class="block p-6 bg-white border rounded-lg hover:bg-gray-50">
                            <h4 class="text-lg font-semibold text-gray-900">Transaction Report</h4>
                            <p class="mt-2 text-sm text-gray-600">View detailed transaction history with advanced filtering options.</p>
                        </a>

                        <!-- Income Report -->
                        <a href="{{ route('reports.income') }}" class="block p-6 bg-white border rounded-lg hover:bg-gray-50">
                            <h4 class="text-lg font-semibold text-gray-900">Income Report</h4>
                            <p class="mt-2 text-sm text-gray-600">Analyze income patterns and sources over time.</p>
                        </a>

                        <!-- Expense Report -->
                        <a href="{{ route('reports.expenses') }}" class="block p-6 bg-white border rounded-lg hover:bg-gray-50">
                            <h4 class="text-lg font-semibold text-gray-900">Expense Report</h4>
                            <p class="mt-2 text-sm text-gray-600">Track and analyze expenses by category and time period.</p>
                        </a>

                        <!-- Category Analysis -->
                        <a href="{{ route('reports.categories') }}" class="block p-6 bg-white border rounded-lg hover:bg-gray-50">
                            <h4 class="text-lg font-semibold text-gray-900">Category Analysis</h4>
                            <p class="mt-2 text-sm text-gray-600">View transaction distribution across different categories.</p>
                        </a>

                        <!-- Entity Report -->
                        <a href="{{ route('reports.entities') }}" class="block p-6 bg-white border rounded-lg hover:bg-gray-50">
                            <h4 class="text-lg font-semibold text-gray-900">Entity Report</h4>
                            <p class="mt-2 text-sm text-gray-600">Analyze transactions by players, staff, members, and sponsors.</p>
                        </a>

                        <!-- Bank Account Report -->
                        <a href="{{ route('reports.bank-accounts') }}" class="block p-6 bg-white border rounded-lg hover:bg-gray-50">
                            <h4 class="text-lg font-semibold text-gray-900">Bank Account Report</h4>
                            <p class="mt-2 text-sm text-gray-600">View bank account balances and transaction history.</p>
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