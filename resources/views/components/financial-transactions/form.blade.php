@props(['transaction' => null, 'bankAccounts'])

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Transaction Type -->
    <div>
        <x-input-label for="type" :value="__('Transaction Type')" />
        <select id="type" name="type" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" required>
            <option value="income" {{ old('type', $transaction?->type) === 'income' ? 'selected' : '' }}>Income</option>
            <option value="expense" {{ old('type', $transaction?->type) === 'expense' ? 'selected' : '' }}>Expense</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('type')" />
    </div>

    <!-- Category -->
    <div>
        <x-input-label for="category" :value="__('Category')" />
        <select id="category" name="category" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" required>
            <option value="sponsorship" {{ old('category', $transaction?->category) === 'sponsorship' ? 'selected' : '' }}>Sponsorship</option>
            <option value="salary" {{ old('category', $transaction?->category) === 'salary' ? 'selected' : '' }}>Salary</option>
            <option value="allowance" {{ old('category', $transaction?->category) === 'allowance' ? 'selected' : '' }}>Allowance</option>
            <option value="equipment" {{ old('category', $transaction?->category) === 'equipment' ? 'selected' : '' }}>Equipment</option>
            <option value="maintenance" {{ old('category', $transaction?->category) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            <option value="other" {{ old('category', $transaction?->category) === 'other' ? 'selected' : '' }}>Other</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('category')" />
    </div>

    <!-- Amount -->
    <div>
        <x-input-label for="amount" :value="__('Amount (LKR)')" />
        <x-text-input id="amount" name="amount" type="number" step="0.01" class="mt-1 block w-full" 
            :value="old('amount', $transaction?->amount)" required />
        <x-input-error class="mt-2" :messages="$errors->get('amount')" />
    </div>

    <!-- Transaction Date -->
    <div>
        <x-input-label for="transaction_date" :value="__('Transaction Date')" />
        <x-text-input id="transaction_date" name="transaction_date" type="date" class="mt-1 block w-full" 
            :value="old('transaction_date', $transaction?->transaction_date?->format('Y-m-d'))" required />
        <x-input-error class="mt-2" :messages="$errors->get('transaction_date')" />
    </div>

    <!-- Bank Account -->
    <div>
        <x-input-label for="bank_account_id" :value="__('Bank Account')" />
        <select id="bank_account_id" name="bank_account_id" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" required>
            @foreach($bankAccounts as $account)
                <option value="{{ $account->id }}" {{ old('bank_account_id', $transaction?->bank_account_id) == $account->id ? 'selected' : '' }}>
                    {{ $account->bank_name }} - {{ $account->account_number }}
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('bank_account_id')" />
    </div>

    <!-- Related Entity -->
    <div>
        <x-input-label for="transactionable_type" :value="__('Related To')" />
        <select id="transactionable_type" name="transactionable_type" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" required>
            <option value="">Select Related Entity</option>
            <option value="{{ App\Models\Member::class }}" {{ old('transactionable_type', $transaction?->transactionable_type) === App\Models\Member::class ? 'selected' : '' }}>Member</option>
            <option value="{{ App\Models\Staff::class }}" {{ old('transactionable_type', $transaction?->transactionable_type) === App\Models\Staff::class ? 'selected' : '' }}>Staff</option>
            <option value="{{ App\Models\Player::class }}" {{ old('transactionable_type', $transaction?->transactionable_type) === App\Models\Player::class ? 'selected' : '' }}>Player</option>
            <option value="{{ App\Models\Sponsor::class }}" {{ old('transactionable_type', $transaction?->transactionable_type) === App\Models\Sponsor::class ? 'selected' : '' }}>Sponsor</option>
            <option value="other" {{ old('transactionable_type', $transaction?->transactionable_type) === 'other' ? 'selected' : '' }}>Other</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('transactionable_type')" />
    </div>

    <!-- Related Entity ID -->
    <div>
        <x-input-label for="transactionable_id" :value="__('Related Entity ID')" />
        <x-text-input id="transactionable_id" name="transactionable_id" type="text" class="mt-1 block w-full" 
            :value="old('transactionable_id', $transaction?->transactionable_id)" />
        <x-input-error class="mt-2" :messages="$errors->get('transactionable_id')" />
    </div>

    <!-- Payment Method -->
    <div>
        <x-input-label for="payment_method" :value="__('Payment Method')" />
        <select id="payment_method" name="payment_method" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" required>
            <option value="cash" {{ old('payment_method', $transaction?->payment_method) === 'cash' ? 'selected' : '' }}>Cash</option>
            <option value="bank_transfer" {{ old('payment_method', $transaction?->payment_method) === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
            <option value="check" {{ old('payment_method', $transaction?->payment_method) === 'check' ? 'selected' : '' }}>Check</option>
            <option value="online" {{ old('payment_method', $transaction?->payment_method) === 'online' ? 'selected' : '' }}>Online</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('payment_method')" />
    </div>

    <!-- Attachments -->
    <div>
        <x-input-label for="attachments" :value="__('Attachments')" />
        <input type="file" id="attachments" name="attachments[]" multiple
            class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
            accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
        <p class="mt-1 text-sm text-gray-600">
            Allowed file types: JPG, JPEG, PNG, PDF, DOC, DOCX (max 2MB each)
        </p>
        <x-input-error class="mt-2" :messages="$errors->get('attachments.*')" />

        @if($transaction && $transaction->attachments)
            <div class="mt-4">
                <h4 class="font-medium text-gray-900">Current Attachments:</h4>
                <div class="mt-2 space-y-2">
                    @foreach($transaction->attachments as $attachment)
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                            <a href="{{ Storage::url($attachment) }}" target="_blank" class="text-sm text-yellow-600 hover:text-yellow-900">
                                {{ basename($attachment) }}
                            </a>
                            <button type="button" onclick="deleteAttachment('{{ $attachment }}')" class="text-red-600 hover:text-red-900">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Signature Section -->
    <div class="md:col-span-2 border-t pt-6 mt-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Signature</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Signature Upload -->
            <div>
                <x-input-label for="signature" :value="__('Signature (PNG only)')" />
                <input type="file" id="signature" name="signature" 
                    class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                    accept=".png">
                <p class="mt-1 text-sm text-gray-600">
                    Please upload a PNG file of your signature (preferably with transparent background)
                </p>
                <x-input-error class="mt-2" :messages="$errors->get('signature')" />
            </div>

            <!-- Signatory Name -->
            <div>
                <x-input-label for="signatory_name" :value="__('Signatory Name')" />
                <x-text-input id="signatory_name" name="signatory_name" type="text" class="mt-1 block w-full" 
                    :value="old('signatory_name', $transaction?->signatory_name)" />
                <x-input-error class="mt-2" :messages="$errors->get('signatory_name')" />
            </div>

            <!-- Signatory Designation -->
            <div>
                <x-input-label for="signatory_designation" :value="__('Signatory Designation')" />
                <x-text-input id="signatory_designation" name="signatory_designation" type="text" class="mt-1 block w-full" 
                    :value="old('signatory_designation', $transaction?->signatory_designation)" />
                <x-input-error class="mt-2" :messages="$errors->get('signatory_designation')" />
            </div>

            @if($transaction && $transaction->signature)
            <div>
                <x-input-label :value="__('Current Signature')" />
                <div class="mt-2">
                    <img src="{{ Storage::url($transaction->signature) }}" alt="Current Signature" class="max-h-20">
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Description -->
    <div class="md:col-span-2">
        <x-input-label for="description" :value="__('Description')" />
        <textarea id="description" name="description" rows="3" 
            class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" required>{{ old('description', $transaction?->description) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    <!-- Reference Number -->
    <div>
        <x-input-label for="reference_number" :value="__('Reference Number')" />
        <x-text-input id="reference_number" name="reference_number" type="text" class="mt-1 block w-full" 
            :value="old('reference_number', $transaction?->reference_number)" />
        <x-input-error class="mt-2" :messages="$errors->get('reference_number')" />
    </div>

    <!-- Status -->
    <div>
        <x-input-label for="status" :value="__('Status')" />
        <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm" required>
            <option value="completed" {{ old('status', $transaction?->status) === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="pending" {{ old('status', $transaction?->status) === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="cancelled" {{ old('status', $transaction?->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('status')" />
    </div>
</div> 