<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Member Details') }}
            </h2>
            <div>
                <a href="{{ route('members.edit', $member) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded mr-2">
                    Edit Member
                </a>
                <form action="{{ route('members.destroy', $member) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded" 
                        onclick="return confirm('Are you sure you want to delete this member?')">
                        Delete Member
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
                                @if($member->profile_picture)
                                    <img src="{{ asset('storage/' . $member->profile_picture) }}" 
                                        alt="{{ $member->first_name }}'s Profile Picture" 
                                        class="h-32 w-32 rounded-full object-cover border-4 border-yellow-200">
                                @else
                                    <div class="h-32 w-32 rounded-full bg-yellow-100 flex items-center justify-center border-4 border-yellow-200">
                                        <span class="text-yellow-800 font-bold text-3xl">
                                            {{ strtoupper(substr($member->first_name, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Membership Number</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $member->membership_number }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Full Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $member->first_name }} {{ $member->last_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">NIC</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $member->nic }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Date of Birth</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $member->date_of_birth ? $member->date_of_birth->format('Y-m-d') : 'Not specified' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Contact Information -->
                        <div class="bg-yellow-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4">Contact Information</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Phone</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $member->phone }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Address</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $member->address }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $member->user->email }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Membership Information -->
                        <div class="bg-yellow-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4">Membership Information</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Joined Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $member->joined_date ? $member->joined_date->format('Y-m-d') : 'Not specified' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Status</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($member->status === 'active') bg-green-100 text-green-800 
                                            @elseif($member->status === 'inactive') bg-gray-100 text-gray-800 
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($member->status) }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Membership Fee</dt>
                                    <dd class="mt-1 text-sm text-gray-900">LKR {{ number_format($member->membership_fee, 2) }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Financial Transactions -->
                        <div class="bg-yellow-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4">Recent Transactions</h3>
                            @if($member->financialTransactions->count() > 0)
                                <div class="space-y-4">
                                    @foreach($member->financialTransactions->take(5) as $transaction)
                                        <div class="border-l-4 border-yellow-400 pl-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $transaction->description }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $transaction->transaction_date->format('Y-m-d') }} - 
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
                            @if($member->attachments && count($member->attachments) > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($member->attachments as $attachment)
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
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 