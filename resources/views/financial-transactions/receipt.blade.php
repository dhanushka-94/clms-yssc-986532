<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $transaction->transaction_number }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #fff;
            color: #333;
            font-size: 12px;
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        .logo {
            margin-bottom: 15px;
        }
        .logo img {
            max-width: 120px;
            max-height: 60px;
            object-fit: contain;
        }
        .club-info {
            margin-bottom: 20px;
        }
        .club-info h1 {
            font-size: 20px;
            color: #2c3e50;
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        .club-info p {
            margin: 4px 0;
            color: #555;
            font-size: 12px;
            line-height: 1.4;
        }
        .receipt-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .receipt-number {
            font-size: 13px;
            color: #666;
            font-weight: 500;
            background: #f8f9fa;
            padding: 4px 10px;
            border-radius: 4px;
            display: inline-block;
        }
        .info-section {
            margin-bottom: 15px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
        }
        .info-row {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        .info-row:last-child {
            margin-bottom: 0;
        }
        .label {
            font-weight: 600;
            width: 140px;
            color: #2c3e50;
            font-size: 12px;
        }
        .value {
            flex: 1;
            color: #444;
            font-size: 12px;
        }
        .amount {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 11px;
            color: #666;
            padding-top: 15px;
            border-top: 2px solid #f0f0f0;
        }
        .status {
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 500;
            font-size: 11px;
            display: inline-block;
        }
        .status-completed {
            color: #0f5132;
            background: #d1e7dd;
        }
        .status-pending {
            color: #664d03;
            background: #fff3cd;
        }
        .status-cancelled {
            color: #842029;
            background: #f8d7da;
        }
        .signature-section {
            margin-top: 20px;
            text-align: right;
            padding: 15px;
            border-top: 1px solid #f0f0f0;
        }
        .signature-image {
            max-width: 120px;
            max-height: 60px;
            margin-bottom: 5px;
            object-fit: contain;
        }
        .signature-info {
            font-size: 11px;
            color: #666;
            line-height: 1.3;
        }
        .related-to {
            background: #e8f4f8;
            padding: 10px 15px;
            border-radius: 4px;
            margin-top: 8px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- Club Logo -->
            @if($clubSettings && $clubSettings->logo_path)
            <div class="logo">
                <img src="{{ public_path('images/' . $clubSettings->logo_path) }}" alt="Club Logo">
            </div>
            @endif

            <!-- Club Info -->
            <div class="club-info">
                <h1>{{ $clubSettings ? $clubSettings->name : config('club.name') }}</h1>
                <p>{{ $clubSettings ? $clubSettings->address : (is_array(config('club.address')) ? implode(', ', array_filter(config('club.address'))) : config('club.address')) }}</p>
                <p>Phone: {{ $clubSettings ? $clubSettings->phone : config('club.phone') }}</p>
                <p>Email: {{ $clubSettings ? $clubSettings->email : config('club.email') }}</p>
                @if($clubSettings && $clubSettings->registration_number)
                    <p>Registration No: {{ $clubSettings->registration_number }}</p>
                @endif
                @if($clubSettings && $clubSettings->tax_number)
                    <p>Tax No: {{ $clubSettings->tax_number }}</p>
                @endif
            </div>

            <div class="receipt-title">TRANSACTION RECEIPT</div>
            <div class="receipt-number">#{{ $transaction->transaction_number }}</div>
        </div>

        <div class="info-section">
            <div class="info-row">
                <span class="label">Date:</span>
                <span class="value">{{ $transaction->transaction_date->format('F d, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="label">Type:</span>
                <span class="value">{{ ucfirst($transaction->type) }}</span>
            </div>
            <div class="info-row">
                <span class="label">Category:</span>
                <span class="value">{{ ucfirst($transaction->category) }}</span>
            </div>
            <div class="info-row">
                <span class="label">Payment Method:</span>
                <span class="value">{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</span>
            </div>
            <div class="info-row">
                <span class="label">Bank Account:</span>
                <span class="value">{{ $transaction->bankAccount->bank_name }} - {{ $transaction->bankAccount->account_number }}</span>
            </div>
            @if($transaction->reference_number)
            <div class="info-row">
                <span class="label">Reference Number:</span>
                <span class="value">{{ $transaction->reference_number }}</span>
            </div>
            @endif
        </div>

        <div class="info-section">
            <div class="info-row">
                <span class="label">Description:</span>
                <span class="value">{{ $transaction->description }}</span>
            </div>
            <div class="info-row">
                <span class="label">Amount:</span>
                <span class="value amount">LKR {{ number_format($transaction->amount, 2) }}</span>
            </div>
            <div class="info-row">
                <span class="label">Status:</span>
                <span class="value">
                    <span class="status status-{{ $transaction->status }}">{{ ucfirst($transaction->status) }}</span>
                </span>
            </div>
        </div>

        @if($transaction->transactionable && $transaction->transactionable_type !== 'other')
        <div class="related-to">
            <div class="info-row">
                <span class="label">Related To:</span>
                <span class="value">
                    {{ class_basename($transaction->transactionable_type) }} - 
                    @switch($transaction->transactionable_type)
                        @case('App\Models\Player')
                        @case('App\Models\Staff')
                        @case('App\Models\Member')
                            {{ $transaction->transactionable->first_name }} {{ $transaction->transactionable->last_name }}
                            @break
                        @case('App\Models\Sponsor')
                            {{ $transaction->transactionable->name }}
                            @break
                        @default
                            {{ $transaction->transactionable->name ?? $transaction->transactionable->company_name ?? 'N/A' }}
                    @endswitch
                </span>
            </div>
        </div>
        @endif

        <!-- Signature Section - Show transaction signature or default system signature -->
        <div class="signature-section">
            <div style="text-align: right; margin-bottom: 10px;">
                <img src="{{ public_path('images/yssc-signature.png') }}" alt="Signature" style="max-width: 150px; max-height: 60px; margin-bottom: 5px;">
                <div style="font-weight: bold; color: #333;">Authorized Signature</div>
                @if(isset($signatoryName))
                    <div style="font-weight: 500; margin-top: 3px;">{{ $signatoryName }}</div>
                @endif
                @if(isset($signatoryDesignation))
                    <div style="color: #666;">{{ $signatoryDesignation }}</div>
                @endif
            </div>
        </div>

        <div class="footer">
            <p>This is a computer generated receipt.</p>
            <p>Generated on: {{ now()->format('F d, Y h:i A') }}</p>
            <p style="color: #2c3e50; font-weight: 500;">{{ $clubSettings->name ?? config('app.name') }} - Building Champions of Tomorrow</p>
        </div>
    </div>
</body>
</html> 