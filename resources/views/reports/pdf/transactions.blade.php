<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transactions Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .summary {
            margin-bottom: 30px;
        }
        .summary-item {
            margin-bottom: 10px;
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
        }
        .income {
            color: #059669;
        }
        .expense {
            color: #dc2626;
        }
        .completed {
            color: #059669;
        }
        .pending {
            color: #d97706;
        }
        .cancelled {
            color: #dc2626;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Transactions Report</h1>
        <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <strong>Total Income:</strong> LKR {{ number_format($totalIncome, 2) }}
        </div>
        <div class="summary-item">
            <strong>Total Expenses:</strong> LKR {{ number_format($totalExpenses, 2) }}
        </div>
        <div class="summary-item">
            <strong>Net Balance:</strong> LKR {{ number_format($totalIncome - $totalExpenses, 2) }}
        </div>
        <div class="summary-item">
            <strong>Pending Transactions:</strong> {{ $pendingTransactions }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
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
            @foreach($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                    <td class="{{ $transaction->type }}">{{ ucfirst($transaction->type) }}</td>
                    <td>{{ $transaction->category }}</td>
                    <td class="{{ $transaction->type }}">
                        LKR {{ number_format($transaction->amount, 2) }}
                    </td>
                    <td class="{{ $transaction->status }}">{{ ucfirst($transaction->status) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</td>
                    <td>{{ $transaction->bankAccount->bank_name }} - {{ $transaction->bankAccount->account_number }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Young Silver Sports Club - Financial Management System</p>
        <p>This is a computer generated report.</p>
    </div>
</body>
</html> 