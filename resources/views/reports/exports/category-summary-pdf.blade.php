<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Category Summary Report</title>
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
        .section {
            margin-bottom: 30px;
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
        .income {
            color: #059669;
        }
        .expense {
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
        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        .percentage-cell {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Category Summary Report</h1>
        <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="date-range">
        <p>
            <strong>Report Period:</strong> 
            @if($dateFrom || $dateTo)
                {{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('Y-m-d') : 'Start' }}
                to
                {{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('Y-m-d') : 'Present' }}
            @else
                All Time
            @endif
        </p>
    </div>

    <!-- Income Categories -->
    <div class="section">
        <h3>Income Categories</h3>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Transaction Count</th>
                    <th>Total Amount</th>
                    <th>% of Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incomeCategories as $category)
                    <tr>
                        <td>{{ $category->category }}</td>
                        <td>{{ $category->transaction_count }}</td>
                        <td class="income">LKR {{ number_format($category->total_amount, 2) }}</td>
                        <td class="percentage-cell">
                            {{ number_format(($category->total_amount / $totalIncome) * 100, 1) }}%
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center;">No income categories found</td>
                    </tr>
                @endforelse
                <tr class="total-row">
                    <td>Total</td>
                    <td>{{ $incomeCategories->sum('transaction_count') }}</td>
                    <td class="income">LKR {{ number_format($totalIncome, 2) }}</td>
                    <td>100%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Expense Categories -->
    <div class="section">
        <h3>Expense Categories</h3>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Transaction Count</th>
                    <th>Total Amount</th>
                    <th>% of Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenseCategories as $category)
                    <tr>
                        <td>{{ $category->category }}</td>
                        <td>{{ $category->transaction_count }}</td>
                        <td class="expense">LKR {{ number_format($category->total_amount, 2) }}</td>
                        <td class="percentage-cell">
                            {{ number_format(($category->total_amount / $totalExpenses) * 100, 1) }}%
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center;">No expense categories found</td>
                    </tr>
                @endforelse
                <tr class="total-row">
                    <td>Total</td>
                    <td>{{ $expenseCategories->sum('transaction_count') }}</td>
                    <td class="expense">LKR {{ number_format($totalExpenses, 2) }}</td>
                    <td>100%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
        <p>Young Silver Sports Club</p>
    </div>
</body>
</html> 