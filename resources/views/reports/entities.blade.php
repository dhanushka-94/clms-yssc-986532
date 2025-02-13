@php
    $colors = [
        'Players' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'bar' => 'bg-blue-500'],
        'Staff' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'bar' => 'bg-yellow-500'],
        'Members' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'bar' => 'bg-green-500'],
        'Sponsors' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'bar' => 'bg-purple-500']
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Entity Analysis Report') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Date Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('reports.entities') }}" method="GET" class="flex items-center space-x-4">
                        <div class="flex-1">
                            <x-input-label for="date_from" :value="__('From Date')" />
                            <x-text-input id="date_from" name="date_from" type="date" class="mt-1 block w-full" :value="$dateFrom"/>
                        </div>
                        <div class="flex-1">
                            <x-input-label for="date_to" :value="__('To Date')" />
                            <x-text-input id="date_to" name="date_to" type="date" class="mt-1 block w-full" :value="$dateTo"/>
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
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Total Income</h3>
                        <p class="text-2xl font-bold text-green-600">LKR {{ number_format($totalIncome, 2) }}</p>
                    </div>
                </div>

                <!-- Total Expenses -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Total Expenses</h3>
                        <p class="text-2xl font-bold text-red-600">LKR {{ number_format($totalExpenses, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Income Distribution -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Income Distribution by Entity</h3>
                    <div class="space-y-4">
                        @forelse($incomeDistribution as $entity)
                            <div class="bg-white rounded-lg">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">{{ $entity->display_name }}</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        LKR {{ number_format($entity->total_amount, 2) }}
                                        ({{ number_format($entity->percentage, 1) }}%)
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="{{ $colors[$entity->display_name]['bar'] }} h-2.5 rounded-full" 
                                         style="width: {{ $entity->percentage }}%">
                                    </div>
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    {{ $entity->transaction_count }} transactions
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500">No income data available for the selected period.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Expense Distribution -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Expense Distribution by Entity</h3>
                    <div class="space-y-4">
                        @forelse($expenseDistribution as $entity)
                            <div class="bg-white rounded-lg">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">{{ $entity->display_name }}</span>
                                    <span class="text-sm font-medium text-gray-900">
                                        LKR {{ number_format($entity->total_amount, 2) }}
                                        ({{ number_format($entity->percentage, 1) }}%)
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="{{ $colors[$entity->display_name]['bar'] }} h-2.5 rounded-full" 
                                         style="width: {{ $entity->percentage }}%">
                                    </div>
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    {{ $entity->transaction_count }} transactions
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500">No expense data available for the selected period.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add any additional charts or visualizations here if needed
        });
    </script>
    @endpush
</x-app-layout> 