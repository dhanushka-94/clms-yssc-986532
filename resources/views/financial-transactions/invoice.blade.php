<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $transaction->transaction_number }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @page {
            margin: 0;
            size: A4;
            padding: 0;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            color: #2d3748;
            line-height: 1.4;
            font-size: 10px;
            margin: 0;
            padding: 20px;
            background: white;
        }
        .header {
            position: relative;
            background: #f8fafc;
            margin: -20px -20px 25px -20px;
            padding: 25px;
            color: #2d3748;
            border-bottom: 1px solid #e2e8f0;
        }
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }
        .brand-section {
            flex: 2;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .logo {
            background: white;
            padding: 12px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            min-width: 100px;
        }
        .logo img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        .company-info {
            color: #2d3748;
            flex: 1;
        }
        .company-info h1 {
            color: #1a202c;
            font-size: 24px;
            margin: 0 0 10px 0;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }
        .company-info p {
            color: #4a5568;
            margin: 4px 0;
            font-size: 12px;
        }
        .invoice-details {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 8px;
            color: #2d3748;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            min-width: 250px;
        }
        .invoice-title {
            font-size: 24px;
            color: #2d3748;
            margin: 0 0 12px 0;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            text-align: right;
        }
        .invoice-number {
            font-size: 14px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
            text-align: right;
        }
        .invoice-meta {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            font-size: 10px;
        }
        .meta-item {
            margin-bottom: 8px;
        }
        .meta-label {
            font-weight: 600;
            color: #4a5568;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .meta-value {
            color: #2d3748;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-completed { background: #e8f5e9; color: #1b5e20; }
        .status-pending { background: #fff3e0; color: #e65100; }
        .status-cancelled { background: #ffebee; color: #b71c1c; }
        .main-content {
            margin: 20px 0;
            display: flex;
            gap: 25px;
        }
        .left-section, .right-section {
            flex: 1;
        }
        .section-title {
            font-size: 12px;
            color: #1a1a1a;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .info-grid {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 6px;
            margin-bottom: 15px;
        }
        .label {
            color: #4a4a4a;
            font-weight: 500;
        }
        .value {
            color: #1a1a1a;
        }
        .transaction-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .transaction-table th {
            background: #f5f5f5;
            padding: 8px;
            text-align: left;
            font-weight: 600;
            color: #1a1a1a;
            border-bottom: 2px solid #e0e0e0;
            font-size: 10px;
        }
        .transaction-table td {
            padding: 8px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 10px;
            color: #1a1a1a;
        }
        .amount-col {
            text-align: right;
            font-family: 'Courier New', monospace;
        }
        .total-row td {
            font-weight: 600;
            color: #1a1a1a;
            border-top: 2px solid #e0e0e0;
            border-bottom: none;
        }
        .signature-section {
            margin-top: 25px;
            display: flex;
            justify-content: flex-end;
        }
        .signature-box {
            text-align: center;
            width: 160px;
        }
        .signature-image {
            max-width: 120px;
            height: 50px;
            margin: 0 auto 8px;
            object-fit: contain;
        }
        .signature-line {
            width: 100%;
            height: 1px;
            background: #e0e0e0;
            margin-bottom: 4px;
        }
        .signature-info {
            font-size: 9px;
            color: #4a4a4a;
        }
        .footer {
            margin-top: 25px;
            text-align: center;
            color: #4a4a4a;
            font-size: 9px;
            border-top: 1px solid #e0e0e0;
            padding-top: 15px;
        }
        .attachments {
            margin-top: 15px;
            font-size: 9px;
            color: #4a4a4a;
        }
        .attachments ul {
            list-style: none;
            padding: 0;
            margin: 3px 0;
        }
        .attachments li {
            margin: 2px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="brand-section">
                @if($clubSettings && $clubSettings->logo_path)
                <div class="logo">
                    <img src="{{ public_path('images/' . $clubSettings->logo_path) }}" alt="Club Logo">
                </div>
                @endif
                
                <div class="company-info">
                    <h1>{{ $clubSettings ? $clubSettings->name : config('club.name') }}</h1>
                    <p>{{ $clubSettings ? $clubSettings->address : config('club.address') }}</p>
                    <p>Phone: {{ $clubSettings ? $clubSettings->phone : config('club.phone') }}</p>
                    <p>Email: {{ $clubSettings ? $clubSettings->email : config('club.email') }}</p>
                    @if($clubSettings && $clubSettings->registration_number)
                        <p>Registration No: {{ $clubSettings->registration_number }}</p>
                    @endif
                </div>
            </div>

            <div class="invoice-details">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-number">#{{ $transaction->transaction_number }}</div>
                <div class="invoice-meta">
                    <div class="meta-item">
                        <div class="meta-label">Issue Date</div>
                        <div class="meta-value">{{ $transaction->transaction_date->format('F d, Y') }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Due Date</div>
                        <div class="meta-value">{{ $transaction->transaction_date->addDays(30)->format('F d, Y') }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Status</div>
                        <div class="meta-value">
                            <span class="status-badge status-{{ $transaction->status }}">{{ ucfirst($transaction->status) }}</span>
                        </div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-label">Payment Terms</div>
                        <div class="meta-value">Net 30</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="left-section">
            <div class="section-title">Bill To</div>
            <div class="info-grid">
                @if($transaction->transactionable)
                    <span class="label">Name:</span>
                    <span class="value">
                        @if($transaction->transactionable instanceof \App\Models\Sponsor)
                            {{ $transaction->transactionable->name }}
                        @elseif($transaction->transactionable instanceof \App\Models\Player || $transaction->transactionable instanceof \App\Models\Staff || $transaction->transactionable instanceof \App\Models\Member)
                            {{ $transaction->transactionable->first_name }} {{ $transaction->transactionable->last_name }}
                        @else
                            {{ $transaction->transactionable->name ?? 'N/A' }}
                        @endif
                    </span>
                    <span class="label">Type:</span>
                    <span class="value">{{ class_basename($transaction->transactionable_type) }}</span>
                    @if(isset($transaction->transactionable->email))
                        <span class="label">Email:</span>
                        <span class="value">{{ $transaction->transactionable->email }}</span>
                    @endif
                    @if(isset($transaction->transactionable->phone))
                        <span class="label">Phone:</span>
                        <span class="value">{{ $transaction->transactionable->phone }}</span>
                    @endif
                @else
                    <span class="label">Name:</span>
                    <span class="value">General Transaction</span>
                @endif
            </div>
        </div>
        <div class="right-section">
            <div class="section-title">Payment Details</div>
            <div class="info-grid">
                <span class="label">Method:</span>
                <span class="value">{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</span>
                <span class="label">Bank:</span>
                <span class="value">{{ $transaction->bankAccount->bank_name }}</span>
                <span class="label">Account:</span>
                <span class="value">{{ $transaction->bankAccount->account_number }}</span>
                @if($transaction->reference_number)
                    <span class="label">Reference:</span>
                    <span class="value">{{ $transaction->reference_number }}</span>
                @endif
            </div>
        </div>
    </div>

    <table class="transaction-table">
        <thead>
            <tr>
                <th style="width: 45%;">Description</th>
                <th style="width: 25%;">Category</th>
                <th style="width: 30%;" class="amount-col">Amount (LKR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $transaction->description ?: 'N/A' }}</td>
                <td>{{ ucfirst($transaction->category) }}</td>
                <td class="amount-col">{{ number_format($transaction->amount, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="2" style="text-align: right;">Total Amount:</td>
                <td class="amount-col">{{ number_format($transaction->amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    @if($transaction->attachments && count($transaction->attachments) > 0)
    <div class="attachments">
        <div class="section-title">Attachments</div>
        <ul>
            @foreach($transaction->attachments as $attachment)
                <li>• {{ basename($attachment) }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($transaction->signature || ($clubSettings && $clubSettings->default_signature))
    <div class="signature-section">
        <div class="signature-box">
            @php
                $signatureData = null;
                $signatoryName = null;
                $signatoryDesignation = null;
                
                // First try to use transaction signature
                if ($transaction->signature) {
                    try {
                        // Try different paths for the transaction signature
                        $paths = [
                            storage_path('app/public/' . $transaction->signature),
                            public_path('storage/' . $transaction->signature),
                            storage_path('app/public/signatures/' . basename($transaction->signature)),
                            public_path('storage/signatures/' . basename($transaction->signature)),
                            '/storage/signatures/' . basename($transaction->signature)
                        ];
                        
                        foreach ($paths as $path) {
                            if (file_exists($path)) {
                                $signatureData = base64_encode(file_get_contents($path));
                                $signatoryName = $transaction->signatory_name;
                                $signatoryDesignation = $transaction->signatory_designation;
                                break;
                            }
                        }
                    } catch (\Exception $e) {
                        // Log error but continue
                        \Log::error('Failed to load transaction signature: ' . $e->getMessage());
                    }
                }
                
                // If no transaction signature, use system default
                if (!$signatureData && $clubSettings && $clubSettings->default_signature) {
                    try {
                        // Try different paths for the default signature
                        $paths = [
                            storage_path('app/public/' . $clubSettings->default_signature),
                            public_path('storage/' . $clubSettings->default_signature),
                            storage_path('app/public/signatures/' . $clubSettings->default_signature),
                            public_path('images/' . $clubSettings->default_signature),
                            public_path('../storage/signatures/' . basename($clubSettings->default_signature)),
                            public_path('storage/signatures/' . basename($clubSettings->default_signature)),
                            '/storage/signatures/' . basename($clubSettings->default_signature)
                        ];
                        
                        foreach ($paths as $path) {
                            if (file_exists($path)) {
                                $signatureData = base64_encode(file_get_contents($path));
                                $signatoryName = $clubSettings->default_signatory_name;
                                $signatoryDesignation = $clubSettings->default_signatory_designation;
                                break;
                            }
                        }
                    } catch (\Exception $e) {
                        // Log error but continue
                        \Log::error('Failed to load default signature: ' . $e->getMessage());
                    }
                }
                
                // If still no signature, use a fallback signature from public/images
                if (!$signatureData) {
                    try {
                        $fallbackPath = public_path('images/club-logo.png');
                        if (file_exists($fallbackPath)) {
                            $signatureData = base64_encode(file_get_contents($fallbackPath));
                            $signatoryName = $clubSettings ? $clubSettings->default_signatory_name : 'Authorized Signatory';
                            $signatoryDesignation = $clubSettings ? $clubSettings->default_signatory_designation : 'Young Silver Sports Club';
                        }
                    } catch (\Exception $e) {
                        // Log error but continue
                        \Log::error('Failed to load fallback signature: ' . $e->getMessage());
                    }
                }
            @endphp
            
            @if($signatureData)
                <img src="data:image/png;base64,{{ $signatureData }}" alt="Signature" class="signature-image">
            @endif
            <div class="signature-line"></div>
            <div class="signature-info">
                <div>Authorized Signature</div>
                @if($signatoryName)
                    <div style="font-weight: 600; margin: 3px 0;">{{ $signatoryName }}</div>
                @endif
                @if($signatoryDesignation)
                    <div>{{ $signatoryDesignation }}</div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <div class="footer">
        <p>{{ $clubSettings->name ?? config('app.name') }} - Building Champions of Tomorrow</p>
    </div>
</body>
</html> 