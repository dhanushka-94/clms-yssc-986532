<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ ucfirst($type) }} Financial Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 20px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-box {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
        }
        .info-box h3 {
            margin: 0 0 10px 0;
            color: #666;
            font-size: 14px;
        }
        .info-box p {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
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
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .text-green {
            color: #059669;
        }
        .text-red {
            color: #DC2626;
        }
        .date-range {
            text-align: right;
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .summary {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ ucfirst($type) }} Financial Report</h1>
        <h2>{{ $type === 'sponsor' ? $model->name : $model->first_name . ' ' . $model->last_name }}</h2>
        @if($type === 'player')
            <p>Player ID: {{ $model->player_id }}</p>
        @elseif($type === 'staff')
            <p>Employee ID: {{ $model->employee_id }}</p>
        @elseif($type === 'member')
            <p>Membership Number: {{ $model->membership_number }}</p>
        @elseif($type === 'sponsor')
            <p>Sponsor ID: {{ $model->sponsor_id }}</p>
        @endif
    </div>

    <div class="date-range">
        <p>
            <strong>Report Period:</strong> 
            @if($date_from || $date_to)
                {{ $date_from ? \Carbon\Carbon::parse($date_from)->format('Y-m-d') : 'Start' }}
                to
                {{ $date_to ? \Carbon\Carbon::parse($date_to)->format('Y-m-d') : 'Present' }}
            @else
                All Time
            @endif
        </p>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <h3>Total Income</h3>
            <p class="text-green">LKR {{ number_format($total_income ?? 0, 2) }}</p>
            <small>{{ $income_count ?? 0 }} transactions</small>
        </div>
        <div class="info-box">
            <h3>Total Expenses</h3>
            <p class="text-red">LKR {{ number_format($total_expenses ?? 0, 2) }}</p>
            <small>{{ $expense_count ?? 0 }} transactions</small>
        </div>
    </div>

    <!-- Transaction Summary -->
    <div class="summary-box" style="margin-bottom: 20px; padding: 10px; background-color: #f8f9fa; border-radius: 5px;">
        <h3 style="margin-bottom: 10px;">Transaction Summary</h3>
        <table style="width: 100%; margin-bottom: 0;">
            <tr>
                <th style="width: 25%;">Total Income:</th>
                <td style="width: 25%;" class="text-green">LKR {{ number_format($total_income ?? 0, 2) }}</td>
                <th style="width: 25%;">Total Expenses:</th>
                <td style="width: 25%;" class="text-red">LKR {{ number_format($total_expenses ?? 0, 2) }}</td>
            </tr>
            <tr>
                <th>Income Transactions:</th>
                <td>{{ $income_count ?? 0 }}</td>
                <th>Expense Transactions:</th>
                <td>{{ $expense_count ?? 0 }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Category</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                    <td>{{ ucfirst($transaction->type) }}</td>
                    <td>{{ $transaction->category }}</td>
                    <td>{{ $transaction->description }}</td>
                    <td class="{{ $transaction->type === 'income' ? 'text-green' : 'text-red' }}">
                        LKR {{ number_format($transaction->amount, 2) }}
                    </td>
                    <td>{{ ucfirst($transaction->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No transactions found</td>
                </tr>
            @endforelse
            <!-- Totals Row -->
            <tr style="font-weight: bold; background-color: #f8f9fa;">
                <td colspan="4" style="text-align: right;">Totals:</td>
                <td class="text-green">LKR {{ number_format($total_income ?? 0, 2) }}</td>
                <td class="text-red">LKR {{ number_format($total_expenses ?? 0, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="summary">
        <h3>Additional Information</h3>
        <table>
            <tr>
                <th>Name</th>
                <td>{{ $type === 'sponsor' ? $model->name : $model->first_name . ' ' . $model->last_name }}</td>
                <th>Status</th>
                <td>{{ ucfirst($model->status) }}</td>
            </tr>
            <tr>
                <th>Contact</th>
                <td>{{ $model->phone }}</td>
                <th>{{ $type === 'sponsor' ? 'Contract End Date' : 'Joined Date' }}</th>
                <td>
                    @if($type === 'sponsor')
                        {{ $model->contract_end_date ? $model->contract_end_date->format('Y-m-d') : 'N/A' }}
                    @else
                        {{ $model->joined_date ? $model->joined_date->format('Y-m-d') : 'N/A' }}
                    @endif
                </td>
            </tr>
            @if($type === 'player')
                <tr>
                    <th>Position</th>
                    <td>{{ $model->position }}</td>
                    <th>Jersey Number</th>
                    <td>{{ $model->jersey_number }}</td>
                </tr>
            @elseif($type === 'staff')
                <tr>
                    <th>Role</th>
                    <td>{{ $model->role }}</td>
                    <th>Contract End Date</th>
                    <td>{{ $model->contract_end_date ? $model->contract_end_date->format('Y-m-d') : 'N/A' }}</td>
                </tr>
            @elseif($type === 'member')
                <tr>
                    <th>Membership Type</th>
                    <td>{{ ucfirst($model->membership_type) }}</td>
                    <th>Designation</th>
                    <td>{{ $model->designation }}</td>
                </tr>
            @elseif($type === 'sponsor')
                <tr>
                    <th>Sponsorship Type</th>
                    <td>{{ ucfirst($model->sponsorship_type ?? 'N/A') }}</td>
                    <th>Sponsorship Amount</th>
                    <td>LKR {{ number_format($model->sponsorship_amount ?? 0, 2) }}</td>
                </tr>
            @endif
        </table>
    </div>

    <div class="footer">
        <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>Young Silver Sports Club</p>
    </div>
</body>
</html> 