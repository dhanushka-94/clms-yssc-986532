<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Transaction') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('financial-transactions.update', $transaction->transaction_number) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @include('financial-transactions.form', [
                            'transaction' => $transaction,
                            'bankAccounts' => $bankAccounts,
                            'players' => $players,
                            'staff' => $staff,
                            'members' => $members,
                            'sponsors' => $sponsors
                        ])

                        <div class="mt-6 flex justify-end">
                            <x-secondary-button type="button" class="mr-3" onclick="window.history.back()">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                            
                            <x-primary-button>
                                {{ __('Update Transaction') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 