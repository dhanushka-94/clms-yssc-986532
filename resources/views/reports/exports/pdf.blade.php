<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #1a1a1a;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .filters {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }
        .filters p {
            margin: 5px 0;
        }
        .income {
            color: #16a34a;
        }
        .expense {
            color: #dc2626;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Financial Transaction Report</h1>
        <p>Generated on: {{ $generatedAt->format('Y-m-d H:i:s') }}</p>
    </div>

    @if(!empty($filters))
    <div class="filters">
        <h3>Applied Filters:</h3>
        @if(!empty($filters['date_from']))
            <p>Date From: {{ $filters['date_from'] }}</p>
        @endif
        @if(!empty($filters['date_to']))
            <p>Date To: {{ $filters['date_to'] }}</p>
        @endif
        @if(!empty($filters['type']))
            <p>Type: {{ ucfirst($filters['type']) }}</p>
        @endif
        @if(!empty($filters['category']))
            <p>Category: {{ ucfirst(str_replace('_', ' ', $filters['category'])) }}</p>
        @endif
        @if(!empty($filters['payment_method']))
            <p>Payment Method: {{ ucfirst(str_replace('_', ' ', $filters['payment_method'])) }}</p>
        @endif
        @if(!empty($filters['status']))
            <p>Status: {{ ucfirst($filters['status']) }}</p>
        @endif
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Transaction Number</th>
                <th>Date</th>
                <th>Type</th>
                <th>Category</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Payment Method</th>
                <th>Bank Account</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->transaction_number }}</td>
                    <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                    <td>{{ ucfirst($transaction->type) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $transaction->category)) }}</td>
                    <td class="{{ $transaction->type }}">
                        LKR {{ number_format($transaction->amount, 2) }}
                    </td>
                    <td>{{ ucfirst($transaction->status) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</td>
                    <td>{{ $transaction->bankAccount->bank_name }} - {{ $transaction->bankAccount->account_number }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">No transactions found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
    </div>
</body>
</html> 