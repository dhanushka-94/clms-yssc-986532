<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Income Report</title>
    <style>
        @page {
            margin: 1cm;
            size: landscape;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0 0 5px 0;
            font-size: 16px;
        }
        .header p {
            margin: 0;
            color: #666;
        }
        .summary {
            margin-bottom: 15px;
            display: table;
            width: 100%;
            border-spacing: 10px;
        }
        .summary-box {
            display: table-cell;
            background-color: #f0fdf4;
            padding: 10px;
            border-radius: 4px;
            width: 33.33%;
        }
        .summary-box h3 {
            margin: 0;
            color: #166534;
            font-size: 12px;
        }
        .summary-box p {
            margin: 3px 0 0;
            font-size: 14px;
            font-weight: bold;
            color: #166534;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9px;
        }
        th, td {
            border: 0.5px solid #ddd;
            padding: 4px 6px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            white-space: nowrap;
        }
        .status {
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 8px;
            display: inline-block;
        }
        .status-completed {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-pending {
            background-color: #fef9c3;
            color: #854d0e;
        }
        .status-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .amount {
            text-align: right;
            white-space: nowrap;
            color: #166534;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
        }
        .filters {
            margin-bottom: 15px;
            font-size: 9px;
            color: #666;
            background-color: #f8f9fa;
            padding: 8px;
            border-radius: 4px;
        }
        .filters ul {
            margin: 5px 0;
            padding-left: 20px;
        }
        .filters li {
            margin-bottom: 2px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Income Report</h1>
        <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="summary">
        <div class="summary-box">
            <h3>Total Income</h3>
            <p>LKR {{ number_format($totalIncome, 2) }}</p>
        </div>
        <div class="summary-box">
            <h3>Monthly Income</h3>
            <p>LKR {{ number_format($monthlyIncome, 2) }}</p>
        </div>
        <div class="summary-box">
            <h3>Yearly Income</h3>
            <p>LKR {{ number_format($yearlyIncome, 2) }}</p>
        </div>
    </div>

    @if($filters['date_from'] || $filters['date_to'] || $filters['category'] || $filters['payment_method'] || $filters['status'])
        <div class="filters">
            <strong>Applied Filters:</strong>
            <ul>
                @if($filters['date_from'])
                    <li>From: {{ $filters['date_from'] }}</li>
                @endif
                @if($filters['date_to'])
                    <li>To: {{ $filters['date_to'] }}</li>
                @endif
                @if($filters['category'])
                    <li>Category: {{ $filters['category'] }}</li>
                @endif
                @if($filters['payment_method'])
                    <li>Payment Method: {{ ucfirst(str_replace('_', ' ', $filters['payment_method'])) }}</li>
                @endif
                @if($filters['status'])
                    <li>Status: {{ ucfirst($filters['status']) }}</li>
                @endif
            </ul>
        </div>
    @endif

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
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                    <td>{{ $transaction->transaction_number }}</td>
                    <td>{{ $transaction->category }}</td>
                    <td>{{ $transaction->description }}</td>
                    <td class="amount">LKR {{ number_format($transaction->amount, 2) }}</td>
                    <td>
                        <span class="status status-{{ $transaction->status }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </td>
                    <td>{{ ucfirst(str_replace('_', ' ', $transaction->payment_method)) }}</td>
                    <td>{{ $transaction->bankAccount->bank_name }} - {{ $transaction->bankAccount->account_number }}</td>
                    <td>{{ $transaction->transactionable_type ? $transaction->transactionable_name : 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center;">No income transactions found.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align: right; font-weight: bold;">Total:</td>
                <td class="amount" style="font-weight: bold;">LKR {{ number_format($totalIncome, 2) }}</td>
                <td colspan="4"></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
        <p>Young Silver Sports Club</p>
    </div>
</body>
</html> 