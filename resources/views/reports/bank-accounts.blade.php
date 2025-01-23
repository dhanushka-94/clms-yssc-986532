<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Bank Account Report') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <x-report-filters 
                :action="route('reports.bank-accounts')"
                :filters="[
                    'date' => true,
                    'type' => true
                ]"
                :bankAccounts="$bankAccounts ?? []"
            />

            <!-- Bank Account Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Bank Account Summary</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Bank Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Account Number
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Account Type
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Branch
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Current Balance
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Income
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Expenses
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($bankAccounts as $account)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $account->bank_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $account->account_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $account->account_type }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $account->branch }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="{{ $account->current_balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                LKR {{ number_format($account->current_balance, 2) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                            LKR {{ number_format($account->total_income, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                            LKR {{ number_format($account->total_expenses, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No bank accounts found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Bank Account Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                <!-- Balance Distribution -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Balance Distribution</h4>
                        <div class="h-80">
                            <canvas id="balanceChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Transaction Volume -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Transaction Volume</h4>
                        <div class="h-80">
                            <canvas id="volumeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bankAccounts = @json($bankAccounts);

            // Balance Distribution Chart
            const balanceCtx = document.getElementById('balanceChart').getContext('2d');
            new Chart(balanceCtx, {
                type: 'pie',
                data: {
                    labels: bankAccounts.map(account => `${account.bank_name} - ${account.account_number}`),
                    datasets: [{
                        data: bankAccounts.map(account => Math.abs(account.current_balance)),
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(139, 92, 246, 0.8)',
                            'rgba(236, 72, 153, 0.8)'
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
                            text: 'Current Balance Distribution'
                        }
                    }
                }
            });

            // Transaction Volume Chart
            const volumeCtx = document.getElementById('volumeChart').getContext('2d');
            new Chart(volumeCtx, {
                type: 'bar',
                data: {
                    labels: bankAccounts.map(account => `${account.bank_name} - ${account.account_number}`),
                    datasets: [
                        {
                            label: 'Income',
                            data: bankAccounts.map(account => account.total_income),
                            backgroundColor: 'rgba(34, 197, 94, 0.8)',
                            borderColor: 'rgb(34, 197, 94)',
                            borderWidth: 1
                        },
                        {
                            label: 'Expenses',
                            data: bankAccounts.map(account => account.total_expenses),
                            backgroundColor: 'rgba(239, 68, 68, 0.8)',
                            borderColor: 'rgb(239, 68, 68)',
                            borderWidth: 1
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
                        legend: {
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Income vs Expenses by Account'
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout> 