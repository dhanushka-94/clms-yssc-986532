<?php

namespace App\Exports;

use App\Models\FinancialTransaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = FinancialTransaction::with(['bankAccount', 'transactionable']);

        if (!empty($this->filters['date_from'])) {
            $query->where('transaction_date', '>=', $this->filters['date_from']);
        }
        if (!empty($this->filters['date_to'])) {
            $query->where('transaction_date', '<=', $this->filters['date_to']);
        }
        if (!empty($this->filters['type'])) {
            $query->where('type', $this->filters['type']);
        }
        if (!empty($this->filters['category'])) {
            $query->where('category', $this->filters['category']);
        }
        if (!empty($this->filters['payment_method'])) {
            $query->where('payment_method', $this->filters['payment_method']);
        }
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }
        if (!empty($this->filters['bank_account_id'])) {
            $query->where('bank_account_id', $this->filters['bank_account_id']);
        }

        return $query->latest('transaction_date');
    }

    public function headings(): array
    {
        return [
            'Transaction Number',
            'Date',
            'Type',
            'Category',
            'Amount',
            'Status',
            'Payment Method',
            'Bank Account',
            'Related To',
            'Description',
            'Reference Number',
            'Receipt Number'
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->transaction_number,
            $transaction->transaction_date->format('Y-m-d'),
            ucfirst($transaction->type),
            ucfirst(str_replace('_', ' ', $transaction->category)),
            number_format($transaction->amount, 2),
            ucfirst($transaction->status),
            ucfirst(str_replace('_', ' ', $transaction->payment_method)),
            $transaction->bankAccount->bank_name . ' - ' . $transaction->bankAccount->account_number,
            $transaction->transactionable_type ? class_basename($transaction->transactionable_type) . ': ' . $transaction->transactionable_name : 'N/A',
            $transaction->description,
            $transaction->reference_number ?? 'N/A',
            $transaction->receipt_number ?? 'N/A'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
} 