<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Club Settings -->
                    <div>
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Club Settings</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Manage your club's basic information, logo, and features.
                                </p>
                            </div>
                            <a href="{{ route('settings.club') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                                Manage Club Settings
                            </a>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="mt-8 border-t border-gray-200 pt-8">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Categories</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Manage categories for sponsors, income, and expenses.
                                </p>
                            </div>
                            <a href="{{ route('settings.categories') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                                Manage Categories
                            </a>
                        </div>
                    </div>

                    <!-- Signature Management -->
                    <div class="mt-8 border-t border-gray-200 pt-8">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Signature Management</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Manage signatures for invoices and other documents.
                                </p>
                            </div>
                            <a href="{{ route('settings.signatures.index') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                                Manage Signatures
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 