<!-- Main Form Container -->
<div class="space-y-8">
    <!-- Transaction Details Section -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Transaction Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Transaction Type -->
            <div>
                <x-input-label for="type" :value="__('Transaction Type *')" />
                <select id="type" name="type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="">Select Type</option>
                    <option value="income" {{ old('type', $transaction?->type ?? '') == 'income' ? 'selected' : '' }}>Income</option>
                    <option value="expense" {{ old('type', $transaction?->type ?? '') == 'expense' ? 'selected' : '' }}>Expense</option>
                </select>
                <x-input-error :messages="$errors->get('type')" class="mt-2" />
            </div>

            <!-- Amount -->
            <div>
                <x-input-label for="amount" :value="__('Amount *')" />
                <x-text-input id="amount" name="amount" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('amount', $transaction?->amount ?? '')" required />
                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
            </div>

            <!-- Transaction Date -->
            <div>
                <x-input-label for="transaction_date" :value="__('Transaction Date *')" />
                <x-text-input id="transaction_date" name="transaction_date" type="date" class="mt-1 block w-full" :value="old('transaction_date', $transaction?->transaction_date?->format('Y-m-d') ?? '')" required />
                <x-input-error :messages="$errors->get('transaction_date')" class="mt-2" />
            </div>

            <!-- Category -->
            <div>
                <x-input-label for="category" :value="__('Category *')" />
                <x-text-input id="category" name="category" type="text" class="mt-1 block w-full" :value="old('category', $transaction?->category ?? '')" required />
                <x-input-error :messages="$errors->get('category')" class="mt-2" />
            </div>

            <!-- Payment Method -->
            <div>
                <x-input-label for="payment_method" :value="__('Payment Method *')" />
                <select id="payment_method" name="payment_method" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="">Select Payment Method</option>
                    <option value="cash" {{ old('payment_method', $transaction?->payment_method ?? '') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="bank_transfer" {{ old('payment_method', $transaction?->payment_method ?? '') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="check" {{ old('payment_method', $transaction?->payment_method ?? '') == 'check' ? 'selected' : '' }}>Check</option>
                    <option value="online" {{ old('payment_method', $transaction?->payment_method ?? '') == 'online' ? 'selected' : '' }}>Online</option>
                </select>
                <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
            </div>

            <!-- Status -->
            <div>
                <x-input-label for="status" :value="__('Status *')" />
                <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="">Select Status</option>
                    <option value="completed" {{ old('status', $transaction?->status ?? '') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ old('status', $transaction?->status ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="cancelled" {{ old('status', $transaction?->status ?? '') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>

            <!-- Bank Account -->
            <div>
                <x-input-label for="bank_account_id" :value="__('Bank Account *')" />
                <select id="bank_account_id" name="bank_account_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="">Select Bank Account</option>
                    @foreach($bankAccounts as $account)
                        <option value="{{ $account->id }}" {{ old('bank_account_id', $transaction?->bank_account_id ?? '') == $account->id ? 'selected' : '' }}>
                            {{ $account->bank_name }} - {{ $account->account_number }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('bank_account_id')" class="mt-2" />
            </div>

            <!-- Transaction For -->
            <div class="col-span-2">
                <x-input-label for="transactionable_type" :value="__('Transaction For')" />
                <select id="transactionable_type" name="transactionable_type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">Select Type</option>
                    <option value="App\Models\Player" {{ old('transactionable_type', $transaction?->transactionable_type ?? '') == 'App\Models\Player' ? 'selected' : '' }}>Player</option>
                    <option value="App\Models\Staff" {{ old('transactionable_type', $transaction?->transactionable_type ?? '') == 'App\Models\Staff' ? 'selected' : '' }}>Staff</option>
                    <option value="App\Models\Member" {{ old('transactionable_type', $transaction?->transactionable_type ?? '') == 'App\Models\Member' ? 'selected' : '' }}>Member</option>
                    <option value="App\Models\Sponsor" {{ old('transactionable_type', $transaction?->transactionable_type ?? '') == 'App\Models\Sponsor' ? 'selected' : '' }}>Sponsor</option>
                </select>
                <x-input-error :messages="$errors->get('transactionable_type')" class="mt-2" />
            </div>

            <!-- Related Entity -->
            <div class="col-span-2" id="related-entity-container" style="display: none;">
                <x-input-label for="transactionable_id" :value="__('Select Entity')" />
                <select id="transactionable_id" name="transactionable_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">Select Entity</option>
                </select>
                <x-input-error :messages="$errors->get('transactionable_id')" class="mt-2" />
            </div>

            <!-- Description -->
            <div class="col-span-2">
                <x-input-label for="description" :value="__('Description *')" />
                <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('description', $transaction?->description ?? '') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <!-- Reference Number -->
            <div>
                <x-input-label for="reference_number" :value="__('Reference Number')" />
                <x-text-input id="reference_number" name="reference_number" type="text" class="mt-1 block w-full" :value="old('reference_number', $transaction?->reference_number ?? '')" />
                <x-input-error :messages="$errors->get('reference_number')" class="mt-2" />
            </div>

            <!-- Receipt Number -->
            <div>
                <x-input-label for="receipt_number" :value="__('Receipt Number')" />
                <x-text-input id="receipt_number" name="receipt_number" type="text" class="mt-1 block w-full" :value="old('receipt_number', $transaction?->receipt_number ?? '')" />
                <x-input-error :messages="$errors->get('receipt_number')" class="mt-2" />
            </div>

            <!-- Attachments -->
            <div class="col-span-2">
                <x-input-label for="attachments" :value="__('Attachments')" />
                <input type="file" id="attachments" name="attachments[]" multiple class="mt-1 block w-full" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                <p class="mt-1 text-sm text-gray-500">Accepted file types: JPG, JPEG, PNG, PDF, DOC, DOCX (max 2MB each)</p>
                <x-input-error :messages="$errors->get('attachments.*')" class="mt-2" />
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex items-center justify-end space-x-3">
        <x-secondary-button onclick="window.history.back()">
            {{ __('Cancel') }}
        </x-secondary-button>
        <x-primary-button>
            {{ isset($transaction) ? __('Update Transaction') : __('Create Transaction') }}
        </x-primary-button>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const transactionableTypeSelect = document.getElementById('transactionable_type');
        const transactionableIdSelect = document.getElementById('transactionable_id');
        const relatedEntityContainer = document.getElementById('related-entity-container');
        
        const players = @json($players);
        const staff = @json($staff);
        const members = @json($members);
        const sponsors = @json($sponsors);
        
        transactionableTypeSelect.addEventListener('change', function() {
            const selectedType = this.value;
            transactionableIdSelect.innerHTML = '<option value="">Select Entity</option>';
            
            if (!selectedType) {
                relatedEntityContainer.style.display = 'none';
                return;
            }
            
            let entities;
            switch(selectedType) {
                case 'App\\Models\\Player':
                    entities = players;
                    break;
                case 'App\\Models\\Staff':
                    entities = staff;
                    break;
                case 'App\\Models\\Member':
                    entities = members;
                    break;
                case 'App\\Models\\Sponsor':
                    entities = sponsors;
                    break;
                default:
                    entities = [];
            }
            
            entities.forEach(entity => {
                const option = document.createElement('option');
                option.value = entity.id;
                option.textContent = entity.first_name ? `${entity.first_name} ${entity.last_name}` : entity.company_name;
                if (entity.id == {{ old('transactionable_id', $transaction?->transactionable_id ?? 'null') }}) {
                    option.selected = true;
                }
                transactionableIdSelect.appendChild(option);
            });
            
            relatedEntityContainer.style.display = 'block';
        });
        
        // Trigger change event if type is already selected (e.g., when editing)
        if (transactionableTypeSelect.value) {
            transactionableTypeSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endpush 