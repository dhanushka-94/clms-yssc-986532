<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Staff Details') }}
            </h2>
            <div>
                <a href="{{ route('staff.edit', $staff) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded mr-2">
                    Edit Staff
                </a>
                <form action="{{ route('staff.destroy', $staff) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded" 
                        onclick="return confirm('Are you sure you want to delete this staff member?')">
                        Delete Staff
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Personal Information -->
                        <div class="bg-yellow-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4">Personal Information</h3>
                            
                            <!-- Profile Picture -->
                            <div class="mb-6 flex justify-center">
                                @if($staff->profile_picture)
                                    <img src="{{ asset('storage/' . $staff->profile_picture) }}" 
                                        alt="{{ $staff->first_name }}'s Profile Picture" 
                                        class="h-32 w-32 rounded-full object-cover border-4 border-yellow-200">
                                @else
                                    <div class="h-32 w-32 rounded-full bg-yellow-100 flex items-center justify-center border-4 border-yellow-200">
                                        <span class="text-yellow-800 font-bold text-3xl">
                                            {{ strtoupper(substr($staff->first_name, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Staff ID</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $staff->employee_id }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Full Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $staff->first_name }} {{ $staff->last_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">NIC</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $staff->nic }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Date of Birth</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $staff->date_of_birth ? $staff->date_of_birth->format('Y-m-d') : 'Not specified' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Contact Information -->
                        <div class="bg-yellow-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4">Contact Information</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Phone</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $staff->phone }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Address</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $staff->address }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $staff->user->email }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Employment Information -->
                        <div class="bg-yellow-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4">Employment Information</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Position</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $staff->role }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Joined Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $staff->joined_date ? $staff->joined_date->format('Y-m-d') : 'Not specified' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Status</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($staff->status === 'active') bg-green-100 text-green-800 
                                            @elseif($staff->status === 'inactive') bg-gray-100 text-gray-800 
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($staff->status) }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Salary</dt>
                                    <dd class="mt-1 text-sm text-gray-900">LKR {{ number_format($staff->salary, 2) }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Recent Transactions -->
                        <div class="bg-yellow-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4">Recent Transactions</h3>
                            @if($staff->financialTransactions->count() > 0)
                                <div class="space-y-4">
                                    @foreach($staff->financialTransactions->take(5) as $transaction)
                                        <div class="border-l-4 border-yellow-400 pl-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $transaction->description }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $transaction->transaction_date ? $transaction->transaction_date->format('Y-m-d') : 'Date not specified' }} - 
                                                LKR {{ number_format($transaction->amount, 2) }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500">No transactions found.</p>
                            @endif
                        </div>

                        <!-- Attachments -->
                        <div class="col-span-2 bg-yellow-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4">Attachments</h3>
                            @if($staff->attachments && count($staff->attachments) > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($staff->attachments as $attachment)
                                        <div class="border border-yellow-200 rounded-lg p-4 bg-white">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1 truncate">
                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                        {{ basename($attachment) }}
                                                    </p>
                                                </div>
                                                <div class="ml-4 flex-shrink-0">
                                                    <a href="{{ asset('storage/' . $attachment) }}" 
                                                       target="_blank"
                                                       class="font-medium text-yellow-600 hover:text-yellow-500">
                                                        View
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500">No attachments found.</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Financial Information</h3>
                        <a href="{{ route('reports.staff.finances', $staff) }}" 
                           class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-md">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            View Financial Report
                        </a>
                    </div>
                    <!-- Financial Summary -->
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-sm font-medium text-green-900">Total Income</h4>
                                    <p class="mt-1 text-2xl font-semibold text-green-600">LKR {{ number_format($totalIncome ?? 0, 2) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-red-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-sm font-medium text-red-900">Total Expenses</h4>
                                    <p class="mt-1 text-2xl font-semibold text-red-600">LKR {{ number_format($totalExpenses ?? 0, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 