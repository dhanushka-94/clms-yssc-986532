<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Expense Report</title>
    <style>
        @page {
            margin: 1cm;
            size: landscape;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-bottom: 1px solid #ddd;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2d3748;
        }
        .summary-boxes {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 10px;
        }
        .summary-box {
            flex: 1;
            padding: 10px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            text-align: center;
        }
        .summary-box h3 {
            margin: 0 0 5px;
            font-size: 14px;
            color: #4a5568;
        }
        .summary-box p {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            color: #2d3748;
        }
        .filters {
            margin-bottom: 20px;
            padding: 10px;
            background: #f8f9fa;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            font-size: 11px;
        }
        .filters strong {
            color: #4a5568;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }
        th {
            background: #f8f9fa;
            padding: 8px;
            border: 1px solid #e2e8f0;
            font-weight: bold;
            text-align: left;
            color: #4a5568;
            white-space: nowrap;
        }
        td {
            padding: 8px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .amount {
            text-align: right;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            padding: 10px 0;
            text-align: center;
            font-size: 10px;
            color: #718096;
            border-top: 1px solid #e2e8f0;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #718096;
            font-style: italic;
        }
        .category-summary {
            margin-bottom: 20px;
        }
        .category-summary h3 {
            margin: 0 0 10px;
            font-size: 14px;
            color: #4a5568;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Expense Report</h1>
        <p>Generated on {{ now()->format('F d, Y') }}</p>
    </div>

    <div class="summary-boxes">
        <div class="summary-box">
            <h3>Total Expenses</h3>
            <p>{{ number_format($totalExpenses, 2) }}</p>
        </div>
        <div class="summary-box">
            <h3>Monthly Expenses</h3>
            <p>{{ number_format($monthlyExpenses, 2) }}</p>
        </div>
        <div class="summary-box">
            <h3>Yearly Expenses</h3>
            <p>{{ number_format($yearlyExpenses, 2) }}</p>
        </div>
    </div>

    <div class="filters">
        <strong>Filters:</strong>
        @if($filters['date_from'] || $filters['date_to'])
            Date Range: {{ $filters['date_from'] ?? 'Any' }} to {{ $filters['date_to'] ?? 'Any' }} |
        @endif
        @if($filters['category'])
            Category: {{ $filters['category'] }} |
        @endif
        @if($filters['payment_method'])
            Payment Method: {{ $filters['payment_method'] }} |
        @endif
        @if($filters['status'])
            Status: {{ $filters['status'] }} |
        @endif
        @if($filters['bank_account_id'])
            Bank Account: {{ $filters['bank_account_id'] }}
        @endif
    </div>

    @if($transactions->isNotEmpty())
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Transaction #</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Payment Method</th>
                    <th>Bank Account</th>
                    <th>Related To</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                        <td>{{ $transaction->transaction_number }}</td>
                        <td>{{ $transaction->category }}</td>
                        <td>{{ $transaction->description }}</td>
                        <td class="amount">{{ number_format($transaction->amount, 2) }}</td>
                        <td>{{ ucfirst($transaction->status) }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</td>
                        <td>{{ $transaction->bankAccount->bank_name }} - {{ $transaction->bankAccount->account_number }}</td>
                        <td>{{ $transaction->transactionable_type ? $transaction->transactionable_name : 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">No transactions found matching the selected criteria.</div>
    @endif

    <div class="footer">
        Page 1
    </div>
</body>
</html> 