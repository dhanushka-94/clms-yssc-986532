<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $transaction->transaction_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .club-logo {
            max-width: 150px;
            margin-bottom: 10px;
        }
        .invoice-title {
            font-size: 24px;
            color: #333;
            margin-bottom: 5px;
        }
        .club-details {
            margin-bottom: 20px;
            font-size: 14px;
            color: #666;
        }
        .invoice-info {
            margin-bottom: 30px;
        }
        .invoice-info table {
            width: 100%;
        }
        .invoice-info td {
            padding: 5px;
            vertical-align: top;
        }
        .transaction-details {
            margin-bottom: 30px;
        }
        .transaction-details table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .transaction-details th, .transaction-details td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .transaction-details th {
            background-color: #f8f8f8;
        }
        .amount {
            text-align: right;
        }
        .total {
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .signature {
            margin-top: 50px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                @if($clubSettings && $clubSettings->logo_path)
                    <img src="{{ storage_path('app/public/' . $clubSettings->logo_path) }}" alt="Club Logo" style="max-width: 200px;">
                @endif
            </div>
            <div class="club-info">
                <h1>{{ $clubSettings->name ?? config('club.name') }}</h1>
                <p>{{ $clubSettings->address ?? config('club.address') }}</p>
                <p>Phone: {{ $clubSettings->phone ?? config('club.phone') }}</p>
                <p>Email: {{ $clubSettings->email ?? config('club.email') }}</p>
                @if($clubSettings->registration_number)
                    <p>Registration No: {{ $clubSettings->registration_number }}</p>
                @endif
                @if($clubSettings->tax_number)
                    <p>Tax No: {{ $clubSettings->tax_number }}</p>
                @endif
            </div>
        </div>

        <div class="invoice-info">
            <table>
                <tr>
                    <td width="50%">
                        <strong>Bill To:</strong><br>
                        @if($transaction->transactionable)
                            {{ class_basename($transaction->transactionable_type) }}: {{ $transaction->transactionable->name }}<br>
                            @if($transaction->transactionable->email)
                                Email: {{ $transaction->transactionable->email }}<br>
                            @endif
                            @if($transaction->transactionable->phone)
                                Phone: {{ $transaction->transactionable->phone }}
                            @endif
                        @else
                            Other
                        @endif
                    </td>
                    <td width="50%" style="text-align: right;">
                        <strong>Invoice Number:</strong> {{ $transaction->transaction_number }}<br>
                        <strong>Date:</strong> {{ $transaction->transaction_date->format('Y-m-d') }}<br>
                        <strong>Payment Method:</strong> {{ ucfirst($transaction->payment_method) }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="transaction-details">
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Reference</th>
                        <th class="amount">Amount (LKR)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $transaction->description }}</td>
                        <td>{{ ucfirst($transaction->category) }}</td>
                        <td>{{ $transaction->reference_number ?: '-' }}</td>
                        <td class="amount">{{ number_format($transaction->amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="total" style="text-align: right;">Total:</td>
                        <td class="amount total">{{ number_format($transaction->amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="signature">
            <p>Authorized Signature: _______________________</p>
            <p>Name: {{ auth()->user()->name }}</p>
            <p>Date: {{ now()->format('Y-m-d') }}</p>
        </div>

        <div class="footer">
            <p>Thank you for your business!</p>
            <p>{{ $clubSettings->name }} - Nurturing Young Sports Talent</p>
        </div>
    </div>
</body>
</html> 