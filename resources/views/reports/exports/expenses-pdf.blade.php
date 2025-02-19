<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Expense Report</title>
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
        <h1>Expense Report</h1>
        <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
        @if(isset($filters['date_from']) || isset($filters['date_to']))
            <p>Period: {{ $filters['date_from'] ?? 'Start' }} to {{ $filters['date_to'] ?? 'Present' }}</p>
        @endif
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <strong>Total Expenses:</strong>
                <span class="expense">LKR {{ number_format($totalExpenses, 2) }}</span>
            </div>
            <div class="summary-item">
                <strong>Monthly Expenses:</strong>
                <span class="expense">LKR {{ number_format($monthlyExpenses, 2) }}</span>
            </div>
            <div class="summary-item">
                <strong>Yearly Expenses:</strong>
                <span class="expense">LKR {{ number_format($yearlyExpenses, 2) }}</span>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Transaction #</th>
                <th>Category</th>
                <th>Amount</th>
                <th>Related To</th>
                <th>Status</th>
                <th>Payment Method</th>
                <th>Bank Account</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                    <td>{{ $transaction->transaction_number }}</td>
                    <td>{{ $transaction->category }}</td>
                    <td class="expense">LKR {{ number_format($transaction->amount, 2) }}</td>
                    <td>
                        @if($transaction->transactionable)
                            @switch($transaction->transactionable_type)
                                @case('App\Models\Player')
                                @case('App\Models\Staff')
                                @case('App\Models\Member')
                                    {{ $transaction->transactionable->first_name }} {{ $transaction->transactionable->last_name }}
                                    ({{ class_basename($transaction->transactionable_type) }})
                                    @break
                                @case('App\Models\Sponsor')
                                    {{ $transaction->transactionable->name }}
                                    ({{ class_basename($transaction->transactionable_type) }})
                                    @break
                            @endswitch
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="status-{{ $transaction->status }}">{{ ucfirst($transaction->status) }}</td>
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

    <div class="totals">
        <div class="totals-row">
            <span class="totals-label">Total Expenses:</span>
            <span class="expense">LKR {{ number_format($totalExpenses, 2) }}</span>
        </div>
        <div class="totals-row">
            <span class="totals-label">Monthly Expenses:</span>
            <span class="expense">LKR {{ number_format($monthlyExpenses, 2) }}</span>
        </div>
        <div class="totals-row">
            <span class="totals-label">Yearly Expenses:</span>
            <span class="expense">LKR {{ number_format($yearlyExpenses, 2) }}</span>
        </div>
    </div>

    <div class="footer">
        @php
            $clubSettings = \App\Models\ClubSettings::first();
        @endphp
        @if($clubSettings && $clubSettings->logo_path)
            <img src="{{ public_path('images/' . $clubSettings->logo_path) }}" alt="Club Logo" style="height: 20px; width: auto; display: inline-block; vertical-align: middle; margin-right: 5px;">
        @endif
        <p>This is a computer-generated document. No signature is required.</p>
        <p>Generated by Young Silver Sports Club Management System</p>
    </div>
</body>
</html> 