@props([
    'action' => '',
    'filters' => [
        'date' => true,
        'type' => false,
        'category' => false,
        'payment_method' => false,
        'status' => false,
        'bank_account' => false,
    ],
    'bankAccounts' => [],
    'categories' => []
])

<div class="bg-white p-4 rounded-lg shadow mb-6">
    <form action="{{ $action }}" method="GET" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if($filters['date'] ?? false)
            <!-- Date Range -->
            <div class="col-span-full md:col-span-2 grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="date_from" :value="__('Date From')" />
                    <x-text-input id="date_from" name="date_from" type="date" class="mt-1 block w-full" 
                        :value="request('date_from')" />
                </div>
                <div>
                    <x-input-label for="date_to" :value="__('Date To')" />
                    <x-text-input id="date_to" name="date_to" type="date" class="mt-1 block w-full" 
                        :value="request('date_to')" />
                </div>
            </div>
            @endif

            @if($filters['type'] ?? false)
            <!-- Transaction Type -->
            <div>
                <x-input-label for="type" :value="__('Type')" />
                <select id="type" name="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">All Types</option>
                    <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                    <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                </select>
            </div>
            @endif

            @if($filters['category'] ?? false)
            <!-- Category -->
            <div>
                <x-input-label for="category" :value="__('Category')" />
                <select id="category" name="category" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->name }}" {{ request('category') === $category->name ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            @if($filters['payment_method'] ?? false)
            <!-- Payment Method -->
            <div>
                <x-input-label for="payment_method" :value="__('Payment Method')" />
                <select id="payment_method" name="payment_method" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">All Methods</option>
                    <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="check" {{ request('payment_method') === 'check' ? 'selected' : '' }}>Check</option>
                    <option value="online" {{ request('payment_method') === 'online' ? 'selected' : '' }}>Online</option>
                </select>
            </div>
            @endif

            @if($filters['status'] ?? false)
            <!-- Status -->
            <div>
                <x-input-label for="status" :value="__('Status')" />
                <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">All Status</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            @endif

            @if($filters['bank_account'] ?? false)
            <!-- Bank Account -->
            <div>
                <x-input-label for="bank_account_id" :value="__('Bank Account')" />
                <select id="bank_account_id" name="bank_account_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">All Accounts</option>
                    @foreach($bankAccounts as $account)
                        <option value="{{ $account->id }}" {{ request('bank_account_id') == $account->id ? 'selected' : '' }}>
                            {{ $account->bank_name }} - {{ $account->account_number }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <x-primary-button>
                    {{ __('Apply Filters') }}
                </x-primary-button>

                @if(request()->hasAny(['date_from', 'date_to', 'type', 'category', 'payment_method', 'status', 'bank_account_id']))
                    <a href="{{ $action }}" class="text-gray-600 hover:text-gray-900">
                        {{ __('Clear Filters') }}
                    </a>
                @endif
            </div>

            <div class="flex items-center space-x-4">
                <form action="{{ route('reports.export.pdf') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="filters" value="{{ json_encode(request()->all()) }}">
                    <x-secondary-button type="submit">
                        {{ __('Export PDF') }}
                    </x-secondary-button>
                </form>

                <form action="{{ route('reports.export.excel') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="filters" value="{{ json_encode(request()->all()) }}">
                    <x-secondary-button type="submit">
                        {{ __('Export Excel') }}
                    </x-secondary-button>
                </form>

                <form action="{{ route('reports.export.csv') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="filters" value="{{ json_encode(request()->all()) }}">
                    <x-secondary-button type="submit">
                        {{ __('Export CSV') }}
                    </x-secondary-button>
                </form>
            </div>
        </div>
    </form>
</div> 