@props(['bankAccount' => null])

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Bank Name -->
    <div>
        <x-input-label for="bank_name" :value="__('Bank Name *')" />
        <x-text-input id="bank_name" name="bank_name" type="text" class="mt-1 block w-full" 
            :value="old('bank_name', $bankAccount?->bank_name)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('bank_name')" />
    </div>

    <!-- Branch Name -->
    <div>
        <x-input-label for="branch_name" :value="__('Branch Name *')" />
        <x-text-input id="branch_name" name="branch_name" type="text" class="mt-1 block w-full" 
            :value="old('branch_name', $bankAccount?->branch_name)" required />
        <x-input-error class="mt-2" :messages="$errors->get('branch_name')" />
    </div>

    <!-- Swift Code -->
    <div>
        <x-input-label for="swift_code" :value="__('Swift Code')" />
        <x-text-input id="swift_code" name="swift_code" type="text" class="mt-1 block w-full" 
            :value="old('swift_code', $bankAccount?->swift_code)" />
        <x-input-error class="mt-2" :messages="$errors->get('swift_code')" />
    </div>

    <!-- Account Name -->
    <div>
        <x-input-label for="account_name" :value="__('Account Name *')" />
        <x-text-input id="account_name" name="account_name" type="text" class="mt-1 block w-full" 
            :value="old('account_name', $bankAccount?->account_name)" required />
        <x-input-error class="mt-2" :messages="$errors->get('account_name')" />
    </div>

    <!-- Account Number -->
    <div>
        <x-input-label for="account_number" :value="__('Account Number *')" />
        <x-text-input id="account_number" name="account_number" type="text" class="mt-1 block w-full" 
            :value="old('account_number', $bankAccount?->account_number)" required />
        <x-input-error class="mt-2" :messages="$errors->get('account_number')" />
    </div>

    <!-- Account Type -->
    <div>
        <x-input-label for="account_type" :value="__('Account Type *')" />
        <select id="account_type" name="account_type" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" required>
            <option value="">Select Type</option>
            <option value="savings" {{ old('account_type', $bankAccount?->account_type) === 'savings' ? 'selected' : '' }}>Savings</option>
            <option value="current" {{ old('account_type', $bankAccount?->account_type) === 'current' ? 'selected' : '' }}>Current</option>
            <option value="fixed_deposit" {{ old('account_type', $bankAccount?->account_type) === 'fixed_deposit' ? 'selected' : '' }}>Fixed Deposit</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('account_type')" />
    </div>

    <!-- Currency -->
    <div>
        <x-input-label for="currency" :value="__('Currency *')" />
        <select id="currency" name="currency" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" required>
            <option value="">Select Currency</option>
            <option value="LKR" {{ old('currency', $bankAccount?->currency) === 'LKR' ? 'selected' : '' }}>LKR</option>
            <option value="USD" {{ old('currency', $bankAccount?->currency) === 'USD' ? 'selected' : '' }}>USD</option>
            <option value="EUR" {{ old('currency', $bankAccount?->currency) === 'EUR' ? 'selected' : '' }}>EUR</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('currency')" />
    </div>

    <!-- Initial Balance -->
    <div>
        <x-input-label for="initial_balance" :value="__('Initial Balance *')" />
        <x-text-input id="initial_balance" name="initial_balance" type="number" step="0.01" class="mt-1 block w-full" 
            :value="old('initial_balance', $bankAccount?->initial_balance)" required />
        <x-input-error class="mt-2" :messages="$errors->get('initial_balance')" />
    </div>

    <!-- Current Balance -->
    <div>
        <x-input-label for="current_balance" :value="__('Current Balance *')" />
        <x-text-input id="current_balance" name="current_balance" type="number" step="0.01" class="mt-1 block w-full" 
            :value="old('current_balance', $bankAccount?->current_balance)" required />
        <x-input-error class="mt-2" :messages="$errors->get('current_balance')" />
    </div>

    <!-- Status -->
    <div>
        <x-input-label for="status" :value="__('Status *')" />
        <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" required>
            <option value="active" {{ old('status', $bankAccount?->status) === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $bankAccount?->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('status')" />
    </div>

    <!-- File Attachments -->
    <x-forms.attachments :model="$bankAccount" />
</div> 