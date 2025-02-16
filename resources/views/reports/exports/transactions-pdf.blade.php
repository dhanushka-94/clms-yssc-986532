<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Transaction Report</title>
    <style>
        @page {
            margin: 1cm;
            size: landscape;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f3f4f6;
        }
        .header h1 {
            color: #1a1a1a;
            margin: 0 0 5px 0;
            font-size: 16px;
        }
        .header p {
            margin: 2px 0;
        }
        .summary {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f3f4f6;
        }
        .summary-grid {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .summary-item {
            margin-right: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9px;
        }
        th, td {
            border: 0.5px solid #ddd;
            padding: 4px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
            white-space: nowrap;
        }
        .income {
            color: #059669;
        }
        .expense {
            color: #dc2626;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 8px;
            color: #666;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        .status-completed {
            color: #059669;
        }
        .status-pending {
            color: #d97706;
        }
        .status-cancelled {
            color: #dc2626;
        }
        .amount-column {
            text-align: right;
            white-space: nowrap;
        }
        .totals {
            margin-top: 10px;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
        .totals-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 3px;
        }
        .totals-label {
            font-weight: bold;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Transaction Report</h1>
        <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
        @if(request('date_from') || request('date_to'))
            <p>Period: {{ request('date_from', 'Start') }} to {{ request('date_to', 'Present') }}</p>
        @endif
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <strong>Total Income:</strong>
                <span class="income">LKR {{ number_format($totalIncome, 2) }}</span>
            </div>
            <div class="summary-item">
                <strong>Total Expenses:</strong>
                <span class="expense">LKR {{ number_format($totalExpenses, 2) }}</span>
            </div>
            <div class="summary-item">
                <strong>Net Balance:</strong>
                <span class="{{ ($totalIncome - $totalExpenses) >= 0 ? 'income' : 'expense' }}">
                    LKR {{ number_format($totalIncome - $totalExpenses, 2) }}
                </span>
            </div>
            <div class="summary-item">
                <strong>Pending Transactions:</strong>
                {{ $pendingTransactions }}
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Transaction #</th>
                <th>Type</th>
                <th>Category</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Payment Method</th>
                <th>Bank Account</th>
                <th>Related To</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                    <td>{{ $transaction->transaction_number }}</td>
                    <td class="{{ $transaction->type }}">{{ ucfirst($transaction->type) }}</td>
                    <td>{{ $transaction->category }}</td>
                    <td class="{{ $transaction->type }}">
                        LKR {{ number_format($transaction->amount, 2) }}
                    </td>
                    <td class="{{ $transaction->status }}">{{ ucfirst($transaction->status) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</td>
                    <td>{{ $transaction->bankAccount->bank_name }} - {{ $transaction->bankAccount->account_number }}</td>
                    <td>{{ $transaction->related_name }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center;">No transactions found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row">
            <span class="totals-label">Total Income:</span>
            <span class="income">LKR {{ number_format($totalIncome, 2) }}</span>
        </div>
        <div class="totals-row">
            <span class="totals-label">Total Expenses:</span>
            <span class="expense">LKR {{ number_format($totalExpenses, 2) }}</span>
        </div>
        <div class="totals-row">
            <span class="totals-label">Net Balance:</span>
            <span class="{{ ($totalIncome - $totalExpenses) >= 0 ? 'income' : 'expense' }}">
                LKR {{ number_format($totalIncome - $totalExpenses, 2) }}
            </span>
        </div>
    </div>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
    </div>
</body>
</html> 