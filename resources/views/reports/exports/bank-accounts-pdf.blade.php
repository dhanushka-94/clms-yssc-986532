<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bank Account Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 3cm;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1a1a1a;
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
            background-color: #f3f4f6;
        }
        .total-row {
            font-weight: bold;
            background-color: #f3f4f6;
        }
        .income {
            color: #059669;
        }
        .expense {
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
        <h1>Bank Account Report</h1>
        <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
        @if($dateFrom && $dateTo)
            <p>Period: {{ $dateFrom }} to {{ $dateTo }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Bank Name</th>
                <th>Account Number</th>
                <th>Account Type</th>
                <th>Branch</th>
                <th>Current Balance</th>
                <th>Total Income</th>
                <th>Total Expenses</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bankAccounts as $account)
                <tr>
                    <td>{{ $account->bank_name }}</td>
                    <td>{{ $account->account_number }}</td>
                    <td>{{ $account->account_type }}</td>
                    <td>{{ $account->branch }}</td>
                    <td>LKR {{ number_format($account->current_balance, 2) }}</td>
                    <td class="income">LKR {{ number_format($account->total_income, 2) }}</td>
                    <td class="expense">LKR {{ number_format($account->total_expenses, 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4">Total</td>
                <td>LKR {{ number_format($bankAccounts->sum('current_balance'), 2) }}</td>
                <td class="income">LKR {{ number_format($bankAccounts->sum('total_income'), 2) }}</td>
                <td class="expense">LKR {{ number_format($bankAccounts->sum('total_expenses'), 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
    </div>
</body>
</html> 