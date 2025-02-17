<x-app-layout>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight header-title">
                {{ __('Player Details') }}
            </h2>
            <div>
                <a href="{{ route('players.download-pdf', $player) }}" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded mr-2">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </a>
                <a href="{{ route('players.edit', $player) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded mr-2 edit-link">
                    Edit Player
                </a>
                <form action="{{ route('players.destroy', $player) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded" 
                        onclick="return confirm('Are you sure you want to delete this player?')">
                        Delete Player
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <!-- Print Styles -->
    <style>
        @media print {
            /* Hide non-printable elements */
            button, .no-print, form, .empty-field {
                display: none !important;
            }

            /* Hide specific sections in print */
            .contract-section,
            .transactions-section,
            .attachments-section,
            .header-title,
            .edit-link {
                display: none !important;
            }

            /* A4 setup */
            @page {
                size: A4;
                margin: 1.5cm;
            }

            /* Reset background colors and borders for print */
            body {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
                font-size: 10pt !important;
                line-height: 1.2 !important;
            }

            /* Remove ALL borders */
            * {
                border: none !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            /* Print header styling */
            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 15px;
                padding-bottom: 10px;
            }

            .print-header h1 {
                font-size: 16pt !important;
                font-weight: bold;
                margin-bottom: 3px;
            }

            .print-header div {
                font-size: 10pt;
            }

            /* Profile section */
            .profile-section {
                text-align: center;
                margin-bottom: 15px;
            }

            .profile-section img,
            .profile-section .rounded-full {
                width: 80px !important;
                height: 80px !important;
                margin: 0 auto;
            }

            /* Information blocks */
            .info-block {
                padding: 10px;
                margin-bottom: 15px;
                break-inside: avoid;
            }

            .info-block h3 {
                font-size: 12pt;
                font-weight: bold;
                margin-bottom: 8px;
                padding-bottom: 3px;
                border-bottom: 1px solid #000 !important; /* Keep only section title underline */
            }

            .info-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
                page-break-inside: avoid;
            }

            .data-row {
                margin-bottom: 5px;
                display: flex;
                justify-content: space-between;
                align-items: baseline;
            }

            .data-label {
                font-weight: bold;
                font-size: 10pt;
                flex: 0 0 40%;
            }

            .data-value {
                font-size: 10pt;
                flex: 0 0 58%;
                text-align: right;
            }

            /* Status badge */
            .status-badge {
                padding: 1px 6px;
                font-size: 9pt;
                background-color: transparent !important;
            }

            /* Remove background colors */
            .bg-yellow-50, 
            .bg-white, 
            .shadow-sm,
            .bg-green-100,
            .bg-red-100,
            .bg-yellow-100,
            .bg-gray-100 {
                background-color: transparent !important;
            }

            /* Remove border colors */
            .border-yellow-200,
            .border-yellow-400,
            .border-gray-200 {
                border: none !important;
            }

            /* Hide empty fields */
            .empty-value {
                display: none !important;
            }

            /* Container adjustments */
            .py-12 {
                padding: 0 !important;
            }

            .p-6 {
                padding: 0 !important;
            }

            .gap-4 {
                gap: 5px !important;
            }

            /* Ensure content fits on one page */
            .max-w-7xl {
                max-width: none !important;
                width: 100% !important;
            }
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Print Header (Only visible when printing) -->
                    <div class="print-header" style="display: none;">
                        <h1 class="text-xl font-bold">YOUNG SILVER SPORTS CLUB</h1>
                        <div class="text-sm mt-1">Player Profile</div>
                    </div>

                    <!-- Profile Picture -->
                    <div class="flex justify-center mb-6 profile-section">
                        @if($player->profile_picture)
                            <img src="{{ asset('storage/' . $player->profile_picture) }}" 
                                alt="{{ $player->first_name }}'s Profile Picture" 
                                class="h-32 w-32 rounded-full object-cover border-4 border-yellow-200">
                        @else
                            <div class="h-32 w-32 rounded-full bg-yellow-100 flex items-center justify-center border-4 border-yellow-200">
                                <span class="text-yellow-800 font-bold text-3xl">
                                    {{ strtoupper(substr($player->first_name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 info-grid">
                        <!-- Personal Information -->
                        <div class="bg-yellow-50 p-6 rounded-lg info-block">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4">Personal Information</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                @if($player->player_id)
                                <div class="data-row">
                                    <dt class="data-label">Player ID</dt>
                                    <dd class="data-value">{{ $player->player_id }}</dd>
                                </div>
                                @endif
                                <div class="data-row">
                                    <dt class="data-label">Full Name</dt>
                                    <dd class="data-value">{{ $player->first_name }} {{ $player->last_name }}</dd>
                                </div>
                                @if($player->nic)
                                <div class="data-row">
                                    <dt class="data-label">NIC</dt>
                                    <dd class="data-value">{{ $player->nic }}</dd>
                                </div>
                                @endif
                                @if($player->date_of_birth)
                                <div class="data-row">
                                    <dt class="data-label">Date of Birth</dt>
                                    <dd class="data-value">{{ $player->date_of_birth->format('Y-m-d') }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>

                        <!-- Contact Information -->
                        <div class="bg-yellow-50 p-6 rounded-lg info-block">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4">Contact Information</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                @if($player->phone)
                                <div class="data-row">
                                    <dt class="data-label">Phone</dt>
                                    <dd class="data-value">{{ $player->phone }}</dd>
                                </div>
                                @endif
                                @if($player->address)
                                <div class="data-row">
                                    <dt class="data-label">Address</dt>
                                    <dd class="data-value">{{ $player->address }}</dd>
                                </div>
                                @endif
                                @if($player->user && $player->user->email)
                                <div class="data-row">
                                    <dt class="data-label">Email</dt>
                                    <dd class="data-value">{{ $player->user->email }}</dd>
                                </div>
                                @endif
                            </dl>
                        </div>

                        <!-- Player Information -->
                        <div class="bg-yellow-50 p-6 rounded-lg info-block">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4">Player Information</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                @if($player->position)
                                <div class="data-row">
                                    <dt class="data-label">Position</dt>
                                    <dd class="data-value">{{ $player->position }}</dd>
                                </div>
                                @endif
                                @if($player->jersey_number)
                                <div class="data-row">
                                    <dt class="data-label">Jersey Number</dt>
                                    <dd class="data-value">{{ $player->jersey_number }}</dd>
                                </div>
                                @endif
                                @if($player->joined_date)
                                <div class="data-row">
                                    <dt class="data-label">Joined Date</dt>
                                    <dd class="data-value">{{ $player->joined_date->format('Y-m-d') }}</dd>
                                </div>
                                @endif
                                @if($player->status)
                                <div class="data-row">
                                    <dt class="data-label">Status</dt>
                                    <dd class="data-value">
                                        <span class="status-badge">{{ ucfirst($player->status) }}</span>
                                    </dd>
                                </div>
                                @endif
                            </dl>
                        </div>

                        <!-- Contract Information -->
                        <div class="bg-yellow-50 p-6 rounded-lg contract-section">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4">Contract Information</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Contract Amount</dt>
                                    <dd class="mt-1 text-sm text-gray-900">LKR {{ number_format($player->contract_amount, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Contract Period</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $player->contract_start_date ? $player->contract_start_date->format('Y-m-d') : 'Not specified' }} to 
                                        {{ $player->contract_end_date ? $player->contract_end_date->format('Y-m-d') : 'Not specified' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-yellow-600">Achievements</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $player->achievements ?? 'No achievements recorded' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Recent Transactions -->
                        <div class="md:col-span-2 bg-yellow-50 p-6 rounded-lg transactions-section">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Financial Transactions
                            </h3>

                            <!-- Transaction Summary -->
                            <div class="mt-4 flex justify-between items-center">
                                <h3 class="text-lg font-medium text-gray-900">Financial Information</h3>
                                <a href="{{ route('reports.player.finances', $player) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-md">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    View Financial Report
                                </a>
                            </div>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-green-50 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="text-sm font-medium text-green-900">Total Income</h4>
                                            <p class="mt-1 text-2xl font-semibold text-green-600">LKR {{ number_format($totalIncome, 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-red-50 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="text-sm font-medium text-red-900">Total Expenses</h4>
                                            <p class="mt-1 text-2xl font-semibold text-red-600">LKR {{ number_format($totalExpenses, 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($player->financialTransactions->count() > 0)
                                <div class="space-y-4">
                                    @foreach($player->financialTransactions as $transaction)
                                        <div class="border-l-4 {{ $transaction->type === 'income' ? 'border-green-400' : 'border-red-400' }} pl-4 py-2 hover:bg-yellow-100 rounded-r transition-colors">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $transaction->description }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ $transaction->transaction_date->format('Y-m-d') }} • 
                                                        {{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }} • 
                                                        {{ $transaction->bankAccount->bank_name }}
                                                    </div>
                                                </div>
                                                <div class="text-sm font-medium {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $transaction->type === 'income' ? '+' : '-' }}
                                                    LKR {{ number_format($transaction->amount, 2) }}
                                                </div>
                                            </div>
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $transaction->type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ ucfirst($transaction->category) }}
                                                </span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 ml-2">
                                                    {{ $transaction->transaction_number }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">No transactions found</p>
                                </div>
                            @endif
                        </div>

                        <!-- Attachments -->
                        <div class="md:col-span-2 bg-yellow-50 p-6 rounded-lg attachments-section">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4">Attachments</h3>
                            @php
                                $attachments = $player->getAttachments();
                            @endphp
                            @if(count($attachments) > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($attachments as $attachment)
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