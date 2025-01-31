@props(['interbankTransfer' => null, 'bankAccounts', 'fromAccountId' => null])

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- From Account -->
    <div>
        <x-input-label for="from_account_id" :value="__('From Account *')" />
        <select id="from_account_id" name="from_account_id" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" required>
            <option value="">Select Source Account</option>
            @foreach($bankAccounts as $account)
                <option value="{{ $account->id }}" 
                    {{ old('from_account_id', $interbankTransfer?->from_account_id ?? $fromAccountId) == $account->id ? 'selected' : '' }}>
                    {{ $account->bank_name }} - {{ $account->account_number }} ({{ $account->currency }} {{ number_format($account->current_balance, 2) }})
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('from_account_id')" />
    </div>

    <!-- To Account -->
    <div>
        <x-input-label for="to_account_id" :value="__('To Account *')" />
        <select id="to_account_id" name="to_account_id" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" required>
            <option value="">Select Destination Account</option>
            @foreach($bankAccounts as $account)
                <option value="{{ $account->id }}" 
                    {{ old('to_account_id', $interbankTransfer?->to_account_id) == $account->id ? 'selected' : '' }}>
                    {{ $account->bank_name }} - {{ $account->account_number }} ({{ $account->currency }})
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('to_account_id')" />
    </div>

    <!-- Amount -->
    <div>
        <x-input-label for="amount" :value="__('Amount *')" />
        <x-text-input id="amount" name="amount" type="number" step="0.01" class="mt-1 block w-full" 
            :value="old('amount', $interbankTransfer?->amount)" required />
        <x-input-error class="mt-2" :messages="$errors->get('amount')" />
    </div>

    <!-- Transfer Date -->
    <div>
        <x-input-label for="transfer_date" :value="__('Transfer Date *')" />
        <x-text-input id="transfer_date" name="transfer_date" type="date" class="mt-1 block w-full" 
            :value="old('transfer_date', $interbankTransfer?->transfer_date?->format('Y-m-d'))" required />
        <x-input-error class="mt-2" :messages="$errors->get('transfer_date')" />
    </div>

    <!-- Description -->
    <div class="md:col-span-2">
        <x-input-label for="description" :value="__('Description *')" />
        <textarea id="description" name="description" rows="3" 
            class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" required>{{ old('description', $interbankTransfer?->description) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    <!-- Reference Number -->
    <div>
        <x-input-label for="reference_number" :value="__('Reference Number')" />
        <x-text-input id="reference_number" name="reference_number" type="text" class="mt-1 block w-full" 
            :value="old('reference_number', $interbankTransfer?->reference_number)" />
        <x-input-error class="mt-2" :messages="$errors->get('reference_number')" />
    </div>

    <!-- Status -->
    <div>
        <x-input-label for="status" :value="__('Status *')" />
        <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" required>
            <option value="pending" {{ old('status', $interbankTransfer?->status) === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="completed" {{ old('status', $interbankTransfer?->status) === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="failed" {{ old('status', $interbankTransfer?->status) === 'failed' ? 'selected' : '' }}>Failed</option>
            <option value="cancelled" {{ old('status', $interbankTransfer?->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('status')" />
    </div>

    <!-- File Attachments -->
    <div class="md:col-span-2">
        <x-input-label for="attachments" :value="__('Attachments')" />
        <input id="attachments" name="attachments[]" type="file" multiple 
            class="mt-1 block w-full" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" />
        <p class="mt-1 text-sm text-gray-500">
            Allowed file types: Images (JPG, JPEG, PNG), Documents (PDF, DOC, DOCX). Maximum file size: 2MB.
        </p>
        <x-input-error class="mt-2" :messages="$errors->get('attachments.*')" />

        @if($interbankTransfer && $interbankTransfer->attachments)
            <div class="mt-4">
                <h4 class="font-medium text-gray-900">Current Attachments:</h4>
                <div class="mt-2 space-y-2">
                    @foreach($interbankTransfer->attachments as $path)
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-600">{{ basename($path) }}</span>
                                <a href="{{ asset('storage/' . $path) }}" target="_blank" 
                                    class="text-yellow-600 hover:text-yellow-900 text-sm">View</a>
                            </div>
                            <div class="flex items-center">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="delete_attachments[]" value="{{ $path }}"
                                        class="rounded border-gray-300 text-yellow-600 shadow-sm focus:border-yellow-300 focus:ring focus:ring-yellow-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Delete</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div> 