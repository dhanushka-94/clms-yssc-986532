<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ ucfirst($type) }} Financial Report: {{ $model->first_name }} {{ $model->last_name }}
            </h2>
            <div class="flex space-x-4">
                <form action="{{ route('reports.export.pdf') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="report_type" value="individual">
                    <input type="hidden" name="model_type" value="{{ $type }}">
                    <input type="hidden" name="model_id" value="{{ $model->id }}">
                    <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                    <input type="hidden" name="total_income" value="{{ $summary->total_income }}">
                    <input type="hidden" name="total_expenses" value="{{ $summary->total_expenses }}">
                    <input type="hidden" name="income_count" value="{{ $summary->income_count }}">
                    <input type="hidden" name="expense_count" value="{{ $summary->expense_count }}">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500">
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Date Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('reports.' . $type . '.finances', $model) }}" method="GET" class="flex items-center space-x-4">
                        <div class="flex-1">
                            <x-input-label for="date_from" :value="__('From Date')" />
                            <x-text-input id="date_from" name="date_from" type="date" class="mt-1 block w-full" :value="request('date_from')"/>
                        </div>
                        <div class="flex-1">
                            <x-input-label for="date_to" :value="__('To Date')" />
                            <x-text-input id="date_to" name="date_to" type="date" class="mt-1 block w-full" :value="request('date_to')"/>
                        </div>
                        <div class="flex-none pt-6">
                            <x-primary-button>Filter</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Total Income -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Income</dt>
                                    <dd class="text-lg font-semibold text-gray-900">LKR {{ number_format($summary->total_income, 2) }}</dd>
                                    <dt class="text-xs text-gray-500 mt-1">{{ $summary->income_count }} transactions</dt>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Expenses -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Expenses</dt>
                                    <dd class="text-lg font-semibold text-gray-900">LKR {{ number_format($summary->total_expenses, 2) }}</dd>
                                    <dt class="text-xs text-gray-500 mt-1">{{ $summary->expense_count }} transactions</dt>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Monthly Trend -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Monthly Trend</h3>
                        <div class="h-80">
                            <canvas id="monthlyTrendChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Category Distribution -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Category Distribution</h3>
                        <div class="h-80">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Transaction History</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $transaction->transaction_date->format('Y-m-d') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $transaction->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($transaction->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $transaction->category }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $transaction->description }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                            LKR {{ number_format($transaction->amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($transaction->status === 'completed') bg-green-100 text-green-800
                                                @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Monthly Trend Chart
            const monthlyTrendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
            new Chart(monthlyTrendCtx, {
                type: 'line',
                data: {
                    labels: @json($monthlyTrend->pluck('month')),
                    datasets: [
                        {
                            label: 'Income',
                            data: @json($monthlyTrend->pluck('income')),
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.1
                        },
                        {
                            label: 'Expenses',
                            data: @json($monthlyTrend->pluck('expenses')),
                            borderColor: 'rgb(239, 68, 68)',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            tension: 0.1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'LKR ' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Monthly Income vs Expenses'
                        }
                    }
                }
            });

            // Category Distribution Chart
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            const incomeCategories = @json($categoryDistribution->where('type', 'income'));
            const expenseCategories = @json($categoryDistribution->where('type', 'expense'));

            new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: [
                        ...incomeCategories.map(c => 'Income: ' + c.category),
                        ...expenseCategories.map(c => 'Expense: ' + c.category)
                    ],
                    datasets: [{
                        data: [
                            ...incomeCategories.map(c => c.total),
                            ...expenseCategories.map(c => c.total)
                        ],
                        backgroundColor: [
                            ...incomeCategories.map(() => 'rgba(34, 197, 94, 0.8)'),
                            ...expenseCategories.map(() => 'rgba(239, 68, 68, 0.8)')
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        },
                        title: {
                            display: true,
                            text: 'Transaction Distribution by Category'
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout> 