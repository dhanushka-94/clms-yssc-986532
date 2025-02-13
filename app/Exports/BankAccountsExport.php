<?php

namespace App\Exports;

use App\Models\BankAccount;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BankAccountsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithColumnFormatting
{
    protected $dateFrom;
    protected $dateTo;

    public function __construct($dateFrom = null, $dateTo = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function collection()
    {
        return BankAccount::select([
            'bank_accounts.*',
            \DB::raw('(SELECT COALESCE(SUM(amount), 0) FROM financial_transactions 
                WHERE financial_transactions.bank_account_id = bank_accounts.id 
                AND type = "income" 
                AND status = "completed"
                ' . ($this->dateFrom ? 'AND transaction_date >= "' . $this->dateFrom . '"' : '') . '
                ' . ($this->dateTo ? 'AND transaction_date <= "' . $this->dateTo . '"' : '') . '
                ) as total_income'),
            \DB::raw('(SELECT COALESCE(SUM(amount), 0) FROM financial_transactions 
                WHERE financial_transactions.bank_account_id = bank_accounts.id 
                AND type = "expense" 
                AND status = "completed"
                ' . ($this->dateFrom ? 'AND transaction_date >= "' . $this->dateFrom . '"' : '') . '
                ' . ($this->dateTo ? 'AND transaction_date <= "' . $this->dateTo . '"' : '') . '
                ) as total_expenses')
        ])->get();
    }

    public function headings(): array
    {
        return [
            'Bank Name',
            'Account Number',
            'Account Type',
            'Branch',
            'Current Balance (LKR)',
            'Total Income (LKR)',
            'Total Expenses (LKR)'
        ];
    }

    public function map($account): array
    {
        return [
            $account->bank_name,
            $account->account_number,
            $account->account_type,
            $account->branch,
            $account->current_balance,  // Raw number for proper Excel formatting
            $account->total_income,     // Raw number for proper Excel formatting
            $account->total_expenses    // Raw number for proper Excel formatting
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Add a totals row at the bottom
        $lastRow = $sheet->getHighestRow() + 1;
        $sheet->setCellValue('A' . $lastRow, 'Total');
        $sheet->mergeCells('A' . $lastRow . ':D' . $lastRow);
        
        // Add sum formulas for the numeric columns
        $sheet->setCellValue('E' . $lastRow, '=SUM(E2:E' . ($lastRow - 1) . ')');
        $sheet->setCellValue('F' . $lastRow, '=SUM(F2:F' . ($lastRow - 1) . ')');
        $sheet->setCellValue('G' . $lastRow, '=SUM(G2:G' . ($lastRow - 1) . ')');

        return [
            1 => ['font' => ['bold' => true]],
            'A' . $lastRow . ':G' . $lastRow => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F3F4F6']
                ]
            ],
        ];
    }
} 