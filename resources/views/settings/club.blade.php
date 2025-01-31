@php
    $clubSettings = \App\Models\ClubSettings::first() ?? new \App\Models\ClubSettings();
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Club Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Club Details Section -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Club Details') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("Update your club's basic information.") }}
                            </p>
                        </header>

                        <form method="post" action="{{ route('settings.update') }}" class="mt-6 space-y-6">
                            @csrf
                            @method('patch')

                            <div>
                                <x-input-label for="name" :value="__('Club Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $clubSettings->name)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $clubSettings->email)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>

                            <div>
                                <x-input-label for="phone" :value="__('Phone')" />
                                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $clubSettings->phone)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                            </div>

                            <div>
                                <x-input-label for="address" :value="__('Address')" />
                                <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $clubSettings->address)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('address')" />
                            </div>

                            <div>
                                <x-input-label for="registration_number" :value="__('Registration Number')" />
                                <x-text-input id="registration_number" name="registration_number" type="text" class="mt-1 block w-full" :value="old('registration_number', $clubSettings->registration_number)" />
                                <x-input-error class="mt-2" :messages="$errors->get('registration_number')" />
                            </div>

                            <div>
                                <x-input-label for="tax_number" :value="__('Tax Number')" />
                                <x-text-input id="tax_number" name="tax_number" type="text" class="mt-1 block w-full" :value="old('tax_number', $clubSettings->tax_number)" />
                                <x-input-error class="mt-2" :messages="$errors->get('tax_number')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Save') }}</x-primary-button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            <!-- Club Logo Section -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Club Logo') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("Update your club's logo.") }}
                            </p>
                        </header>

                        <div class="mt-6">
                            <x-club-logo size="large" />
                        </div>

                        <form method="post" action="{{ route('settings.club.logo') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                            @csrf

                            <div>
                                <x-input-label for="logo" :value="__('New Logo')" />
                                <x-text-input id="logo" name="logo" type="file" class="mt-1 block w-full" accept="image/*" required />
                                <x-input-error class="mt-2" :messages="$errors->get('logo')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Save') }}</x-primary-button>
                            </div>
                        </form>

                        @if($clubSettings && $clubSettings->logo_path)
                            <form method="post" action="{{ route('settings.club.logo.delete') }}" class="mt-6">
                                @csrf
                                @method('delete')
                                <x-danger-button onclick="return confirm('Are you sure you want to remove the logo?')">
                                    {{ __('Remove Logo') }}
                                </x-danger-button>
                            </form>
                        @endif
                    </section>
                </div>
            </div>

            <!-- Default Signature Section -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Default Signature') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("Set up a default signature for financial documents.") }}
                            </p>
                        </header>

                        <!-- Signature Image Form -->
                        <form method="post" action="{{ route('settings.club.signature') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                            @csrf
                            @method('patch')

                            <!-- Current Default Signature -->
                            @if($clubSettings && $clubSettings->default_signature)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Default Signature</label>
                                <img src="{{ Storage::url($clubSettings->default_signature) }}" alt="Default Signature" class="h-24 mb-4">
                            </div>
                            @endif

                            <!-- Upload New Default Signature -->
                            <div>
                                <x-input-label for="default_signature" :value="__('New Default Signature')" />
                                <input type="file" 
                                       name="default_signature" 
                                       accept=".png"
                                       class="block w-full text-sm text-gray-500
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-yellow-50 file:text-yellow-700
                                              hover:file:bg-yellow-100
                                              border border-gray-300 rounded-md">
                                <x-input-error class="mt-2" :messages="$errors->get('default_signature')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Upload Signature') }}</x-primary-button>

                                @if($clubSettings && $clubSettings->default_signature)
                                    <form method="post" action="{{ route('settings.club.signature.delete') }}" class="inline">
                                        @csrf
                                        @method('delete')
                                        <x-danger-button onclick="return confirm('Are you sure you want to remove the default signature?')">
                                            {{ __('Remove Signature') }}
                                        </x-danger-button>
                                    </form>
                                @endif
                            </div>
                        </form>

                        <!-- Signatory Details Form -->
                        <form method="post" action="{{ route('settings.club.signature') }}" class="mt-6 space-y-6">
                            @csrf
                            @method('patch')

                            <!-- Default Signatory Name -->
                            <div>
                                <x-input-label for="default_signatory_name" :value="__('Default Signatory Name')" />
                                <x-text-input id="default_signatory_name" 
                                             name="default_signatory_name" 
                                             type="text" 
                                             class="mt-1 block w-full" 
                                             :value="old('default_signatory_name', $clubSettings->default_signatory_name ?? '')" />
                                <x-input-error class="mt-2" :messages="$errors->get('default_signatory_name')" />
                            </div>

                            <!-- Default Signatory Designation -->
                            <div>
                                <x-input-label for="default_signatory_designation" :value="__('Default Signatory Designation')" />
                                <x-text-input id="default_signatory_designation" 
                                             name="default_signatory_designation" 
                                             type="text" 
                                             class="mt-1 block w-full" 
                                             :value="old('default_signatory_designation', $clubSettings->default_signatory_designation ?? '')" />
                                <x-input-error class="mt-2" :messages="$errors->get('default_signatory_designation')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Save Signatory Details') }}</x-primary-button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            <!-- Categories Section -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header class="flex justify-between items-center">
                            <div>
                                <h2 class="text-lg font-medium text-gray-900">
                                    {{ __('Categories') }}
                                </h2>

                                <p class="mt-1 text-sm text-gray-600">
                                    {{ __("Manage categories for sponsors, income, and expenses.") }}
                                </p>
                            </div>
                            <a href="{{ route('settings.categories') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                {{ __('Manage Categories') }}
                            </a>
                        </header>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 