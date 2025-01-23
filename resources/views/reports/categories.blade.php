<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Category Analysis') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <x-report-filters 
                :action="route('reports.categories')"
                :filters="[
                    'date' => true,
                ]"
                :bankAccounts="$bankAccounts ?? []"
            />

            <!-- Category Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Category Summary</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Category
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Amount
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Transaction Count
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Average Amount
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($categorySummary as $summary)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ ucfirst(str_replace('_', ' ', $summary->category)) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $summary->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($summary->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="{{ $summary->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                                LKR {{ number_format($summary->total_amount, 2) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $summary->transaction_count }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="{{ $summary->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                                LKR {{ number_format($summary->total_amount / $summary->transaction_count, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No category data found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Category Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Category Distribution</h3>
                    <div class="w-full h-96">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('categoryChart').getContext('2d');
            
            // Prepare data for chart
            const categories = @json($categorySummary->pluck('category'));
            const incomeData = @json($categorySummary->where('type', 'income')->pluck('total_amount'));
            const expenseData = @json($categorySummary->where('type', 'expense')->pluck('total_amount'));

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: categories,
                    datasets: [
                        {
                            label: 'Income',
                            data: incomeData,
                            backgroundColor: 'rgba(34, 197, 94, 0.2)',
                            borderColor: 'rgb(34, 197, 94)',
                            borderWidth: 1
                        },
                        {
                            label: 'Expenses',
                            data: expenseData,
                            backgroundColor: 'rgba(239, 68, 68, 0.2)',
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
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Income and Expenses by Category'
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout> 