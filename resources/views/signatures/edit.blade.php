<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Signature') }}
            </h2>
            <a href="{{ route('signatures.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('signatures.update', $transaction) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Current Signature -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Signature</label>
                            @if($transaction->signature)
                                <img src="{{ $transaction->signature }}" alt="Current Signature" class="h-24 mb-4">
                            @else
                                <p class="text-gray-500 text-sm">No signature uploaded</p>
                            @endif
                        </div>

                        <!-- Upload New Signature -->
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Upload New Signature</label>
                            <input type="file" 
                                   name="signature_file" 
                                   accept=".png"
                                   class="block w-full text-sm text-gray-500
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-md file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-yellow-50 file:text-yellow-700
                                          hover:file:bg-yellow-100
                                          border border-gray-300 rounded-md">
                            <p class="mt-1 text-sm text-gray-500">Upload a PNG file of your signature (transparent background recommended)</p>
                            @error('signature_file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Signatory Name -->
                        <div>
                            <label for="signatory_name" class="block text-sm font-medium text-gray-700">Signatory Name</label>
                            <input type="text" 
                                   name="signatory_name" 
                                   id="signatory_name" 
                                   value="{{ old('signatory_name', $transaction->signatory_name) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm">
                            @error('signatory_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Designation -->
                        <div>
                            <label for="signatory_designation" class="block text-sm font-medium text-gray-700">Designation</label>
                            <input type="text" 
                                   name="signatory_designation" 
                                   id="signatory_designation" 
                                   value="{{ old('signatory_designation', $transaction->signatory_designation) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm">
                            @error('signatory_designation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                                Update Signature
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 