<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $transaction->transaction_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .receipt-number {
            font-size: 16px;
            color: #666;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            margin-bottom: 10px;
        }
        .label {
            font-weight: bold;
            width: 150px;
            display: inline-block;
        }
        .amount {
            font-size: 18px;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .status {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
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
    </style>
</head>
<body>
    <div class="header">
        <div class="receipt-title">YSSC - Transaction Receipt</div>
        <div class="receipt-number">{{ $transaction->transaction_number }}</div>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="label">Date:</span>
            <span>{{ $transaction->transaction_date->format('Y-m-d') }}</span>
        </div>
        <div class="info-row">
            <span class="label">Type:</span>
            <span>{{ ucfirst($transaction->type) }}</span>
        </div>
        <div class="info-row">
            <span class="label">Category:</span>
            <span>{{ ucfirst($transaction->category) }}</span>
        </div>
        <div class="info-row">
            <span class="label">Payment Method:</span>
            <span>{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</span>
        </div>
        <div class="info-row">
            <span class="label">Bank Account:</span>
            <span>{{ $transaction->bankAccount->bank_name }} - {{ $transaction->bankAccount->account_number }}</span>
        </div>
        @if($transaction->reference_number)
        <div class="info-row">
            <span class="label">Reference Number:</span>
            <span>{{ $transaction->reference_number }}</span>
        </div>
        @endif
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="label">Description:</span>
            <span>{{ $transaction->description }}</span>
        </div>
        <div class="info-row">
            <span class="label">Amount:</span>
            <span class="amount">LKR {{ number_format($transaction->amount, 2) }}</span>
        </div>
        <div class="info-row">
            <span class="label">Status:</span>
            <span class="status status-{{ $transaction->status }}">{{ ucfirst($transaction->status) }}</span>
        </div>
    </div>

    @if($transaction->transactionable && $transaction->transactionable_type !== 'other')
    <div class="info-section">
        <div class="info-row">
            <span class="label">Related To:</span>
            <span>
                {{ class_basename($transaction->transactionable_type) }} - 
                {{ $transaction->transactionable->name ?? $transaction->transactionable->company_name ?? 'N/A' }}
            </span>
        </div>
    </div>
    @endif

    <div class="footer">
        <p>This is a computer generated receipt.</p>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html> 