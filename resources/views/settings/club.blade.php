@php
    $clubSettings = \App\Models\ClubSettings::first();
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Club Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
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

            <!-- Features Section -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Features') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("Enable or disable features for your club.") }}
                            </p>
                        </header>

                        <form method="post" action="{{ route('settings.club.features') }}" class="mt-6 space-y-6">
                            @csrf

                            <div class="space-y-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="features[]" value="members" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ in_array('members', $enabledFeatures ?? []) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-600">{{ __('Members Management') }}</span>
                                </label>

                                <label class="flex items-center">
                                    <input type="checkbox" name="features[]" value="staff" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ in_array('staff', $enabledFeatures ?? []) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-600">{{ __('Staff Management') }}</span>
                                </label>

                                <label class="flex items-center">
                                    <input type="checkbox" name="features[]" value="players" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ in_array('players', $enabledFeatures ?? []) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-600">{{ __('Players Management') }}</span>
                                </label>

                                <label class="flex items-center">
                                    <input type="checkbox" name="features[]" value="sponsors" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ in_array('sponsors', $enabledFeatures ?? []) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-600">{{ __('Sponsors Management') }}</span>
                                </label>

                                <label class="flex items-center">
                                    <input type="checkbox" name="features[]" value="events" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ in_array('events', $enabledFeatures ?? []) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-600">{{ __('Events Management') }}</span>
                                </label>

                                <label class="flex items-center">
                                    <input type="checkbox" name="features[]" value="finances" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ in_array('finances', $enabledFeatures ?? []) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-600">{{ __('Financial Management') }}</span>
                                </label>
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Save') }}</x-primary-button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 